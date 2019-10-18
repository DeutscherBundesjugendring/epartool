import React from 'react';
import PropTypes from 'prop-types';
import FollowUpTimeline from '../../components/FollowUpTimeline/FollowUpTimeline';
import FollowUpDocumentModal from '../../components/FollowUpDocumentModal/FollowUpDocumentModal';
import downloadFile from '../../service/downloadFile';
import resolveElement from '../../service/resolveElement';
import {
  fetchFollowUpElement,
  fetchFollowUpElementParents,
  fetchFollowUpElementChildren,
  fetchFollowUpDocument,
  fetchFollowUpDocumentSnippets,
  likeFollowUpDocumentSnippet,
  dislikeFollowUpDocumentSnippet,
} from '../../actions';

/* global baseUrl */
/* global followupTranslations */

class FollowUpContainer extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      hasError: false,
      isInitialLoad: true,
      isLoading: false,
      modal: null,
      opened: {},
      rows: [[]],
    };

    this.collapseHandler = this.collapseHandler.bind(this);
    this.isOpened = this.isOpened.bind(this);
    this.handleError = this.handleError.bind(this);
  }

  componentDidMount() {
    const {
      followUpType,
      followUpId,
    } = this.props;

    fetchFollowUpElement(followUpType, followUpId)
      .then((response) => {
        const resolvedElement = this.prepareResolveElement(
          followUpType,
          followUpId,
          response,
        );

        this.setState({
          isInitialLoad: false,
          rows: [[resolvedElement]],
        });

        return response;
      })
      .catch(this.handleError);
  }

  getElementRowIndex(followUpType, followUpId) {
    const { rows } = this.state;
    let colIndex = null;

    rows.forEach((row, index) => {
      row.forEach((element) => {
        if (!colIndex && followUpType === element.props.type && followUpId === element.props.id) {
          colIndex = index;
        }
      });
    });

    return colIndex;
  }

  getParents(followUpType, followUpId) {
    const rowIndex = this.getElementRowIndex(followUpType, followUpId);
    let clearParentalRows = true;
    this.setState({ isLoading: true });

    fetchFollowUpElementParents(followUpType, followUpId)
      .then((multipleResponse) => {
        multipleResponse.forEach((response) => {
          const { rows } = this.state;
          const resolvedElement = this.prepareResolveElement(
            response.type,
            parseInt(response.id, 10),
            response,
          );

          let rowsArr = Object.assign([], rows);
          if (clearParentalRows) {
            clearParentalRows = false;

            rowsArr = rowsArr.slice(rowIndex);
            rowsArr.unshift([]);

            rowsArr[1] = rowsArr[1].filter((element) => {
              if (followUpType === element.props.type && followUpId === element.props.id) {
                return element;
              }
              return null;
            });
          }
          rowsArr[0].push(resolvedElement);

          this.setState({
            isLoading: false,
            rows: rowsArr,
          });
          this.newRowHandler(0);
        });

        return multipleResponse;
      })
      .catch(this.handleError);
  }

  getChildren(followUpType, followUpId) {
    const rowIndex = this.getElementRowIndex(followUpType, followUpId);
    let clearChildRows = true;
    this.setState({ isLoading: true });

    fetchFollowUpElementChildren(followUpType, followUpId)
      .then((multipleResponse) => {
        multipleResponse.forEach((response) => {
          const { rows } = this.state;
          const resolvedElement = this.prepareResolveElement(
            response.type,
            parseInt(response.id, 10),
            response,
          );

          let rowsArr = Object.assign([], rows);
          if (clearChildRows) {
            clearChildRows = false;

            rowsArr = rowsArr.slice(0, rowIndex + 1);
            rowsArr.push([]);

            rowsArr[rowIndex] = rowsArr[rowIndex].filter((element) => {
              if (followUpType === element.props.type && followUpId === element.props.id) {
                return element;
              }
              return null;
            });
          }
          rowsArr[rowIndex + 1].push(resolvedElement);
          this.setState({
            isLoading: false,
            rows: rowsArr,
          });
          this.newRowHandler(rowIndex + 1);
        });

        return multipleResponse;
      })
      .catch(this.handleError);
  }

  getDocumentModal(
    elementResponse,
    rewriteSnippetId = null,
    rewriteSnippetLike = null,
    rewriteSnippetDislike = null,
  ) {
    if (elementResponse.type === 'snippet' || elementResponse.type === 'document') {
      this.setState({ isLoading: true });
      const documentPromise = fetchFollowUpDocument(elementResponse.data.ffid);
      const documentSnippetsPromise = fetchFollowUpDocumentSnippets(elementResponse.data.ffid);

      Promise.all([documentPromise, documentSnippetsPromise])
        .then((responses) => {
          const [documentResponse, snippetResponse] = responses;
          const resolvedElement = (
            <FollowUpDocumentModal
              type={documentResponse.type}
              title={documentResponse.titl}
              author={documentResponse.who}
              description={documentResponse.ref_view}
              date={new Date(documentResponse.when)}
              dateMonthYearOnly={!!documentResponse.is_only_month_year_showed}
              previewImageLink={documentResponse.gfx_who}
              downloadAction={() => {
                downloadFile(documentResponse.ref_doc);
              }}
              downloadLabel={followupTranslations.downloadLabel}
              typeActionLabel={followupTranslations.typeActionLabel}
              typeEndLabel={followupTranslations.typeEndLabel}
              typeRejectedLabel={followupTranslations.typeRejectedLabel}
              typeSupportingLabel={followupTranslations.typeSupportingLabel}
              snippets={snippetResponse.map((response) => ({
                dislikeAction: () => this.modalSnippetDislike(response.fid, elementResponse),
                dislikeCount: parseInt(
                  rewriteSnippetId && rewriteSnippetDislike
                    ? rewriteSnippetDislike
                    : response.lknay,
                  10,
                ),
                dislikeLabel: followupTranslations.dislikeLabel,
                followPathAction: () => {
                  if (elementResponse.id === response.fid) {
                    window.removeModalOpenFromBody();
                    this.setState({ modal: null });
                  } else {
                    window.location = `${baseUrl}/followup/show-by-snippet/kid/${documentResponse.kid}/fid/${response.fid}`;
                  }
                },
                followPathLabel: elementResponse.id === response.fid
                  ? followupTranslations.backToTimeline
                  : followupTranslations.followPath,
                likeAction: () => this.modalSnippetLike(response.fid, elementResponse),
                likeCount: parseInt(
                  rewriteSnippetId === response.fid && rewriteSnippetLike
                    ? rewriteSnippetLike
                    : response.lkyea,
                  10,
                ),
                likeLabel: followupTranslations.likeLabel,
                showFollowPathButton: response.parents_count !== 0 || response.children_count !== 0,
                snippetExplanation: response.expl,
                votingLimitError: followupTranslations.votingLimitError,
              }))}
              closeAction={() => {
                window.removeModalOpenFromBody();
                this.setState({ modal: null });
              }}
            />
          );

          window.addModalOpenToBody();
          this.setState({
            isLoading: false,
            modal: resolvedElement,
          });

          return responses;
        })
        .catch(this.handleError);
    }
  }

  collapseHandler(followUpType, followUpId, isToggle = true) {
    const { opened } = this.state;
    const newOpenedMap = { ...opened };
    newOpenedMap[followUpType + followUpId] = isToggle
      ? !this.isOpened(followUpType, followUpId)
      : false;
    this.setState({ opened: newOpenedMap });
  }

  rowCollapse(rowIndex) {
    const { rows } = this.state;
    rows[rowIndex].forEach((element) => {
      this.collapseHandler(element.props.type, element.props.id, false);
    });
  }

  isOpened(followUpType, followUpId) {
    const { opened } = this.state;
    return !!opened[followUpType + followUpId];
  }

  modalSnippetLike(followUpId, response) {
    likeFollowUpDocumentSnippet(followUpId)
      .then((likeReponse) => {
        const { rows } = this.state;
        this.getDocumentModal(response, followUpId, likeReponse.lkyea);

        let changedRows = Object.assign([], rows);
        changedRows = changedRows.map((row) => (
          row.map((element) => {
            if (element.props.type === 'snippet' && element.props.id === parseInt(followUpId, 10)) {
              const responseCopy = Object.assign([], response);
              responseCopy.data.lkyea = likeReponse.lkyea;

              return this.prepareResolveElement('snippet', followUpId, responseCopy);
            }

            return element;
          })
        ));

        this.setState({ rows: changedRows });
        return likeReponse;
      })
      .catch(this.handleError);
  }

  modalSnippetDislike(followUpId, response) {
    dislikeFollowUpDocumentSnippet(followUpId)
      .then((dislikeResponse) => {
        const { rows } = this.state;
        this.getDocumentModal(response, followUpId, null, dislikeResponse.lknay);

        let changedRows = Object.assign([], rows);
        changedRows = changedRows.map((row) => (
          row.map((element) => {
            if (element.props.type === 'snippet' && element.props.id === parseInt(followUpId, 10)) {
              const responseCopy = Object.assign([], response);
              responseCopy.data.lknay = dislikeResponse.lknay;

              return this.prepareResolveElement('snippet', followUpId, responseCopy);
            }

            return element;
          })
        ));

        this.setState({ rows: changedRows });
        return dislikeResponse;
      })
      .catch(this.handleError);
  }

  newRowHandler(rowIndex) {
    const { rows } = this.state;
    document.getElementById(`row${rowIndex}`).scrollIntoView();
    rows.forEach(
      (rowElement, ind) => this.rowCollapse(ind),
    );
  }

  snippetLike(followUpType, followUpId, response) {
    likeFollowUpDocumentSnippet(followUpId)
      .then((likeResponse) => {
        const { rows } = this.state;
        let changedRows = Object.assign([], rows);
        changedRows = changedRows.map((row) => (
          row.map((element) => {
            if (element.props.type === 'snippet' && element.props.id === followUpId) {
              const responseCopy = Object.assign([], response);
              responseCopy.data.lkyea = likeResponse.lkyea;

              return this.prepareResolveElement(followUpType, followUpId, responseCopy);
            }

            return element;
          })
        ));

        this.setState({ rows: changedRows });
        return likeResponse;
      })
      .catch(this.handleError);
  }

  snippetDislike(followUpType, followUpId, response) {
    dislikeFollowUpDocumentSnippet(followUpId)
      .then((dislikeResponse) => {
        const { rows } = this.state;
        let changedRows = Object.assign([], rows);
        changedRows = changedRows.map((row) => (
          row.map((element) => {
            if (element.props.type === 'snippet' && element.props.id === followUpId) {
              const responseCopy = Object.assign([], response);
              responseCopy.data.lknay = dislikeResponse.lknay;

              return this.prepareResolveElement(followUpType, followUpId, responseCopy);
            }

            return element;
          })
        ));

        this.setState({ rows: changedRows });
        return dislikeResponse;
      })
      .catch(this.handleError);
  }

  prepareResolveElement(followUpType, followUpId, response) {
    return resolveElement(
      response,
      () => this.getParents(followUpType, parseInt(followUpId, 10)),
      () => this.getChildren(followUpType, parseInt(followUpId, 10)),
      () => this.getDocumentModal(response),
      () => this.collapseHandler(followUpType, followUpId),
      () => this.isOpened(followUpType, followUpId),
      followUpType === 'snippet' ? {
        snippetDislikeAction: () => this.snippetDislike(followUpType, followUpId, response),
        snippetLikeAction: () => this.snippetLike(followUpType, followUpId, response),
      } : null,
    );
  }

  handleError() {
    this.setState({ hasError: true });
  }

  render() {
    const {
      hasError,
      isLoading,
      isInitialLoad,
      rows,
      modal,
    } = this.state;
    const { consultationId } = this.props;
    if (hasError) {
      return (
        <p className="alert alert-error text-center">
          {followupTranslations.generalError}
        </p>
      );
    }

    return (
      <FollowUpTimeline
        collapseHandler={this.collapseHandler}
        infoLink={`${baseUrl}/followup/index/kid/${consultationId}`}
        infoLinkTitle={followupTranslations.backToReactionsAndSnippets}
        infoText={followupTranslations.help}
        isLoading={isLoading}
        isInitialLoad={isInitialLoad}
        rows={rows}
        shouldCollapse={this.shouldCollapse}
        modal={modal}
      />
    );
  }
}

FollowUpContainer.propTypes = {
  consultationId: PropTypes.number.isRequired,
  followUpId: PropTypes.number.isRequired,
  followUpType: PropTypes.oneOf(['contribution', 'snippet']).isRequired,
};

export default FollowUpContainer;

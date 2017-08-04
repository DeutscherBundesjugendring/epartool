import React from 'react';
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
      columns: [[]],
      modal: null,
      hasError: false,
    };

    this.handleError = this.handleError.bind(this);
  }

  componentDidMount() {
    const { followUpType, followUpId } = this.props;

    fetchFollowUpElement(followUpType, followUpId)
      .then((response) => {
        const resolvedElement = this.prepareResolveElement(
          followUpType,
          followUpId,
          response
        );

        this.setState({ columns: [[resolvedElement]] });
      })
      .catch(this.handleError);
  }

  getElementColumnIndex(followUpType, followUpId) {
    let colIndex = null;

    this.state.columns.forEach((column, index) => {
      column.forEach((element) => {
        if (!colIndex && followUpType === element.props.type && followUpId === element.props.id) {
          colIndex = index;
        }
      });
    });

    return colIndex;
  }

  getParents(followUpType, followUpId) {
    const columnIndex = this.getElementColumnIndex(followUpType, followUpId);
    let clearParentalColumns = true;

    fetchFollowUpElementParents(followUpType, followUpId)
      .then((multipleResponse) => {
        multipleResponse.forEach((response) => {
          const resolvedElement = this.prepareResolveElement(
            response.type,
            parseInt(response.id, 10),
            response
          );

          let columns = Object.assign([], this.state.columns);
          if (clearParentalColumns) {
            clearParentalColumns = false;

            columns = columns.slice(columnIndex);
            columns.unshift([]);

            columns[1] = columns[1].filter((element) => {
              if (followUpType === element.props.type && followUpId === element.props.id) {
                return element;
              }
              return null;
            });
          }
          columns[0].push(resolvedElement);

          this.setState({ columns });
        });
      })
      .catch(this.handleError);
  }

  getChildren(followUpType, followUpId) {
    const columnIndex = this.getElementColumnIndex(followUpType, followUpId);
    let clearChildColumns = true;

    fetchFollowUpElementChildren(followUpType, followUpId)
      .then((multipleResponse) => {
        multipleResponse.forEach((response) => {
          const resolvedElement = this.prepareResolveElement(
            response.type,
            parseInt(response.id, 10),
            response
          );

          let columns = Object.assign([], this.state.columns);
          if (clearChildColumns) {
            clearChildColumns = false;

            columns = columns.slice(0, columnIndex + 1);
            columns.push([]);

            columns[columnIndex] = columns[columnIndex].filter((element) => {
              if (followUpType === element.props.type && followUpId === element.props.id) {
                return element;
              }
              return null;
            });
          }
          columns[columnIndex + 1].push(resolvedElement);

          this.setState({ columns });
        });
      })
      .catch(this.handleError);
  }

  getDocumentModal(
    elementResponse,
    rewriteSnippetId = null,
    rewriteSnippetLike = null,
    rewriteSnippetDislike = null
  ) {
    if (elementResponse.type === 'snippet' || elementResponse.type === 'document') {
      const documentPromise = fetchFollowUpDocument(elementResponse.data.ffid);
      const documentSnippetsPromise = fetchFollowUpDocumentSnippets(elementResponse.data.ffid);

      Promise.all([documentPromise, documentSnippetsPromise]).then((responses) => {
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
            snippets={snippetResponse.map(response => ({
              snippetExplanation: response.expl,
              likeAction: () => this.modalSnippetLike(response.fid, elementResponse),
              likeCount: parseInt(
                rewriteSnippetId === response.fid && rewriteSnippetLike
                  ? rewriteSnippetLike
                  : response.lkyea,
                10
              ),
              likeLabel: followupTranslations.likeLabel,
              dislikeAction: () => this.modalSnippetDislike(response.fid, elementResponse),
              dislikeCount: parseInt(
                rewriteSnippetId && rewriteSnippetDislike
                  ? rewriteSnippetDislike
                  : response.lknay,
                10
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
              showFollowPathButton: response.parents_count !== 0 || response.children_count !== 0,
              votingLimitError: followupTranslations.votingLimitError,
            }))}
            closeAction={() => {
              window.removeModalOpenFromBody();
              this.setState({ modal: null });
            }}
          />
        );

        window.addModalOpenToBody();
        this.setState({ modal: resolvedElement });
      })
      .catch(this.handleError);
    }
  }

  modalSnippetLike(followUpId, response) {
    likeFollowUpDocumentSnippet(followUpId)
      .then((likeReponse) => {
        this.getDocumentModal(response, followUpId, likeReponse.lkyea);

        let changedColumns = Object.assign([], this.state.columns);
        changedColumns = changedColumns.map(column => (
          column.map((element) => {
            if (element.props.type === 'snippet' && element.props.id === parseInt(followUpId, 10)) {
              const responseCopy = Object.assign([], response);
              responseCopy.data.lkyea = likeReponse.lkyea;

              return this.prepareResolveElement('snippet', followUpId, responseCopy);
            }

            return element;
          })
        ));

        this.setState({ columns: changedColumns });
      })
      .catch(this.handleError);
  }

  modalSnippetDislike(followUpId, response) {
    dislikeFollowUpDocumentSnippet(followUpId)
      .then((dislikeResponse) => {
        this.getDocumentModal(response, followUpId, null, dislikeResponse.lknay);

        let changedColumns = Object.assign([], this.state.columns);
        changedColumns = changedColumns.map(column => (
          column.map((element) => {
            if (element.props.type === 'snippet' && element.props.id === parseInt(followUpId, 10)) {
              const responseCopy = Object.assign([], response);
              responseCopy.data.lknay = dislikeResponse.lknay;

              return this.prepareResolveElement('snippet', followUpId, responseCopy);
            }

            return element;
          })
        ));

        this.setState({ columns: changedColumns });
      })
      .catch(this.handleError);
  }

  snippetLike(followUpType, followUpId, response) {
    likeFollowUpDocumentSnippet(followUpId)
      .then((likeResponse) => {
        let changedColumns = Object.assign([], this.state.columns);
        changedColumns = changedColumns.map(column => (
          column.map((element) => {
            if (element.props.type === 'snippet' && element.props.id === followUpId) {
              const responseCopy = Object.assign([], response);
              responseCopy.data.lkyea = likeResponse.lkyea;

              return this.prepareResolveElement(followUpType, followUpId, responseCopy);
            }

            return element;
          })
        ));

        this.setState({ columns: changedColumns });
      })
      .catch(this.handleError);
  }

  snippetDislike(followUpType, followUpId, response) {
    dislikeFollowUpDocumentSnippet(followUpId)
      .then((dislikeResponse) => {
        let changedColumns = Object.assign([], this.state.columns);
        changedColumns = changedColumns.map(column => (
          column.map((element) => {
            if (element.props.type === 'snippet' && element.props.id === followUpId) {
              const responseCopy = Object.assign([], response);
              responseCopy.data.lknay = dislikeResponse.lknay;

              return this.prepareResolveElement(followUpType, followUpId, responseCopy);
            }

            return element;
          })
        ));

        this.setState({ columns: changedColumns });
      })
      .catch(this.handleError);
  }

  prepareResolveElement(followUpType, followUpId, response) {
    return resolveElement(
      response,
      () => this.getParents(followUpType, parseInt(followUpId, 10)),
      () => this.getChildren(followUpType, parseInt(followUpId, 10)),
      () => this.getDocumentModal(response),
      followUpType === 'snippet' ? {
        snippetLikeAction: () => this.snippetLike(followUpType, followUpId, response),
        snippetDislikeAction: () => this.snippetDislike(followUpType, followUpId, response),
      } : null
    );
  }

  handleError() {
    this.setState({ hasError: true });
  }

  render() {
    if (this.state.hasError) {
      return (
        <p className="alert alert-error text-center">
          {followupTranslations.generalError}
        </p>
      );
    }

    return (
      <FollowUpTimeline
        infoLink={`${baseUrl}/followup/index/kid/${this.props.consultationId}`}
        infoLinkTitle={followupTranslations.backToReactionsAndSnippets}
        infoText={followupTranslations.help}
        columns={this.state.columns}
        modal={this.state.modal}
      />
    );
  }
}

FollowUpContainer.propTypes = {
  consultationId: React.PropTypes.number.isRequired,
  followUpType: React.PropTypes.oneOf(['contribution', 'snippet']).isRequired,
  followUpId: React.PropTypes.number.isRequired,
};

export default FollowUpContainer;

import React from 'react';
import FollowUpTimeline from '../FollowUpTimeline/FollowUpTimeline';
import FollowUpDocumentModal from '../FollowUpDocumentModal/FollowUpDocumentModal';
import resolveElement from '../../service/resolveElement';
import {
  fetchFollowUpElement,
  fetchFollowUpElementParents,
  fetchFollowUpElementChildren,
  fetchFollowUpDocument,
  fetchFollowUpDocumentSnippets,
} from '../../actions';


class FollowUpContainer extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      columns: [[]],
      modal: null,
      hasError: false,
    };
  }

  componentDidMount() {
    const { followUpType, followUpId } = this.props;

    fetchFollowUpElement(followUpType, followUpId)
      .then((response) => {
        const resolvedElement = resolveElement(
          response,
          () => this.getParents(followUpType, followUpId),
          () => this.getChildren(followUpType, followUpId),
          () => this.getDocumentBox(response)
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
          const resolvedElement = resolveElement(
            response,
            () => this.getParents(response.type, parseInt(response.id, 10)),
            () => this.getChildren(response.type, parseInt(response.id, 10)),
            () => this.getDocumentBox(response)
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
          const resolvedElement = resolveElement(
            response,
            () => this.getParents(response.type, parseInt(response.id, 10)),
            () => this.getChildren(response.type, parseInt(response.id, 10)),
            () => this.getDocumentBox(response)
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

  getDocumentBox(elementResponse) {
    if (elementResponse.type === 'snippet' || elementResponse.type === 'document') {
      fetchFollowUpDocument(elementResponse.data.ffid)
        .then((documentResponse) => {
          fetchFollowUpDocumentSnippets(elementResponse.data.ffid)
            .then((snippetResponse) => {
              const resolvedElement = (
                <FollowUpDocumentModal
                  title={documentResponse.titl}
                  author={documentResponse.who}
                  description={documentResponse.ref_view}
                  date={new Date(documentResponse.when)}
                  dateMonthYearOnly={!!documentResponse.is_only_month_year_showed}
                  previewImageLink={documentResponse.gfx_who}
                  downloadAction={() => {
                    window.location = documentResponse.ref_doc;
                  }}
                  downloadLabel="Herunterladen"
                  snippets={snippetResponse.map(response => ({
                    snippetExplanation: response.expl,
                    likeAction: () => {},
                    likeCount: response.lkyea,
                    dislikeAction: () => {},
                    dislikeCount: response.lkyea,
                    followPathAction: () => {
                      if (elementResponse.id === response.ffid) {
                        this.setState({ modal: null });
                      } else {
                        window.location = `/followup/show-by-snippet/kid/${documentResponse.kid}/fid/${response.fid}`;
                      }
                    },
                    followPathLabel: elementResponse.id === response.ffid ? 'Zurück zur Zeitleiste' : 'Folge Verlauf',
                  }))}
                  closeAction={() => this.setState({ modal: null })}
                />
              );

              this.setState({ modal: resolvedElement });
            })
            .catch(this.handleError);
        })
        .catch(this.handleError);
    }
  }

  handleError(error) {
    console.log(error);
    this.setState({ hasError: true });
  }

  render() {
    if (this.state.hasError) {
      return (
        <p className="alert-error text-center">
          Error occurred. Followup timeline cannot be loaded.
        </p>
      );
    }

    return (
      <FollowUpTimeline
        infoLink={`/followup/index/kid/${this.props.consultationId}`}
        infoLinkTitle="Zurück zur Übersicht Reaktionen & Wirkungen"
        infoText={
          'Verfolge hier, welche Reaktionen es auf den Beitrag gab. ' +
          'Klicke auf die Pfeile für nächste Schritte.'
        }
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

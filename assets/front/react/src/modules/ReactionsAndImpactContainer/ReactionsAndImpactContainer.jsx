/**
 * Created by bedrich-schindler on 29.5.17.
 */
import React from 'react';
import FollowUpDocumentModal from '../../components/FollowUpDocumentModal/FollowUpDocumentModal';
import downloadFile from '../../service/downloadFile';
import {
  fetchFollowUpDocument,
  fetchFollowUpDocumentSnippets,
  likeFollowUpDocumentSnippet,
  dislikeFollowUpDocumentSnippet,
} from '../../actions';


/* global baseUrl */
/* global followupTranslations */

class ReactionsAndImpactContainer extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      modal: null,
      hasError: false,
    };
  }

  componentDidMount() {
    this.getDocumentModal();
  }

  getDocumentModal(
    rewriteSnippetId = null,
    rewriteSnippetLike = null,
    rewriteSnippetDislike = null
  ) {
    const documentPromise = fetchFollowUpDocument(this.props.followUpId);
    const documentSnippetsPromise = fetchFollowUpDocumentSnippets(this.props.followUpId);

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
          snippets={snippetResponse.map(response => ({
            snippetExplanation: response.expl,
            likeAction: () => this.modalSnippetLike(response.fid),
            likeCount: parseInt(
              rewriteSnippetId === response.fid && rewriteSnippetLike
                ? rewriteSnippetLike
                : response.lkyea,
              10
            ),
            dislikeAction: () => this.modalSnippetDislike(response.fid),
            dislikeCount: parseInt(
              rewriteSnippetId === response.fid && rewriteSnippetDislike
                ? rewriteSnippetDislike
                : response.lknay,
              10
            ),
            followPathAction: () => {
              window.location = `${baseUrl}/followup/show-by-snippet/kid/${documentResponse.kid}/fid/${response.fid}`;
            },
            followPathLabel: followupTranslations.followPath,
            showFollowPathButton: response.parents_count !== 0 || response.children_count !== 0,
          }))}
          closeAction={() => {
            window.removeModalOpenFromBody();
            this.setState({ modal: null });
          }}
        />
      );

      this.setState({ modal: resolvedElement });
    })
      .catch(this.handleError);
  }

  modalSnippetLike(followUpId) {
    likeFollowUpDocumentSnippet(followUpId)
      .then((response) => {
        this.getDocumentModal(followUpId, response.lkyea);
      })
      .catch(this.handleError);
  }

  modalSnippetDislike(followUpId) {
    dislikeFollowUpDocumentSnippet(followUpId)
      .then((response) => {
        this.getDocumentModal(followUpId, null, response.lknay);
      })
      .catch(this.handleError);
  }

  handleError() {
    this.setState({ hasError: true });
  }

  render() {
    if (this.state.hasError) {
      return (
        <p className="alert-error text-center">
          {followupTranslations.generalError}
        </p>
      );
    }

    return this.state.modal;
  }
}

ReactionsAndImpactContainer.propTypes = {
  followUpId: React.PropTypes.number.isRequired,
};

export default ReactionsAndImpactContainer;

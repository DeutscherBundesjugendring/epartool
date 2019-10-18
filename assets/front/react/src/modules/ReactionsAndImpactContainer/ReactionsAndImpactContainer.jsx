import React from 'react';
import PropTypes from 'prop-types';
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
      hasError: false,
      modal: null,
    };
  }

  componentDidMount() {
    this.getDocumentModal();
  }

  getDocumentModal(
    rewriteSnippetId = null,
    rewriteSnippetLike = null,
    rewriteSnippetDislike = null,
  ) {
    const { followUpId } = this.props;
    const documentPromise = fetchFollowUpDocument(followUpId);
    const documentSnippetsPromise = fetchFollowUpDocumentSnippets(followUpId);

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
            downloadAction={() => downloadFile(documentResponse.ref_doc)}
            downloadLabel={followupTranslations.downloadLabel}
            typeActionLabel={followupTranslations.typeActionLabel}
            typeEndLabel={followupTranslations.typeEndLabel}
            typeRejectedLabel={followupTranslations.typeRejectedLabel}
            typeSupportingLabel={followupTranslations.typeSupportingLabel}
            snippets={snippetResponse.map((response) => ({
              dislikeAction: () => this.modalSnippetDislike(response.fid),
              dislikeCount: parseInt(
                rewriteSnippetId === response.fid && rewriteSnippetDislike
                  ? rewriteSnippetDislike
                  : response.lknay,
                10,
              ),
              dislikeLabel: followupTranslations.dislikeLabel,
              followPathAction: () => {
                window.location = `${baseUrl}/followup/show-by-snippet/kid/${documentResponse.kid}/fid/${response.fid}`;
              },
              followPathLabel: followupTranslations.followPath,
              likeAction: () => this.modalSnippetLike(response.fid),
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

        this.setState({ modal: resolvedElement });
        return resolvedElement;
      })
      .catch(this.handleError);
  }

  modalSnippetLike(followUpId) {
    likeFollowUpDocumentSnippet(followUpId)
      .then((response) => this.getDocumentModal(followUpId, response.lkyea))
      .catch(this.handleError);
  }

  modalSnippetDislike(followUpId) {
    dislikeFollowUpDocumentSnippet(followUpId)
      .then((response) => this.getDocumentModal(followUpId, null, response.lknay))
      .catch(this.handleError);
  }

  handleError() {
    this.setState({ hasError: true });
  }

  render() {
    const {
      hasError,
      modal,
    } = this.state;
    if (hasError) {
      return (
        <p className="alert alert-error text-center">
          {followupTranslations.generalError}
        </p>
      );
    }

    return modal;
  }
}

ReactionsAndImpactContainer.propTypes = {
  followUpId: PropTypes.number.isRequired,
};

export default ReactionsAndImpactContainer;

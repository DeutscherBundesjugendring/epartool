/**
 * Created by bedrich-schindler on 29.5.17.
 */
import React from 'react';
import FollowUpDocumentModal from '../../components/FollowUpDocumentModal/FollowUpDocumentModal';
import {
  fetchFollowUpDocument,
  fetchFollowUpDocumentSnippets,
} from '../../actions';


/* global baseUrl */

class ReactionsAndImpactContainer extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      modal: null,
      hasError: false,
    };
  }

  componentDidMount() {
    const { followUpId } = this.props;

    const documentPromise = fetchFollowUpDocument(followUpId);
    const documentSnippetsPromise = fetchFollowUpDocumentSnippets(followUpId);

    Promise.all([documentPromise, documentSnippetsPromise]).then((responses) => {
      const [documentResponse, snippetResponse] = responses;

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
            likeCount: parseInt(response.lkyea, 10),
            dislikeAction: () => {},
            dislikeCount: parseInt(response.lkyea, 10),
            followPathAction: () => {
              window.location = `${baseUrl}/followup/show-by-snippet/kid/${documentResponse.kid}/fid/${response.fid}`;
            },
            followPathLabel: 'Folge Verlauf',
          }))}
          closeAction={() => this.setState({ modal: null })}
        />
      );

      this.setState({ modal: resolvedElement });
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
          Error occurred. Document modal cannot be loaded.
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

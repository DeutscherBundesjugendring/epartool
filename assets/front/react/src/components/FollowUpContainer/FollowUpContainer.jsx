import React from 'react';
import FollowUpTimeline from '../FollowUpTimeline/FollowUpTimeline';
import resolveElement from '../../service/resolveElement';
import { fetchFollowUpElement } from '../../actions';


class FollowUpContainer extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      columns: [[]],
    };
  }

  componentDidMount() {
    const { followUpType, followUpId } = this.props;

    fetchFollowUpElement(followUpType, followUpId)
      .then((response) => {
        if (response.type === 'contribution' || response.type === 'snippet') {
          const resolvedElement = resolveElement(
            response,
            () => console.log('Parent'),
            () => console.log('Child')
          );

          this.setState({
            columns: [[resolvedElement]],
          });
        }
      })
      .catch(error => console.error(error));
  }

  render() {
    return (
      <FollowUpTimeline
        infoLink={`/followup/index/kid/${this.props.consultationId}`}
        infoLinkTitle="Zurück zur Übersicht Reaktionen & Wirkungen"
        infoText={
          'Verfolge hier, welche Reaktionen es auf den Beitrag gab. ' +
          'Klicke auf die Pfeile für nächste Schritte.'
        }
        columns={this.state.columns}
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

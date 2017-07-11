import React from 'react';
import ThumbButton from '../../components/ThumbButton/ThumbButton';

class Reputation extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      voted: props.voted,
      expectLikesUpdate: false,
      votingLimitErrorShowed: false,
      initLikeCount: props.likeCount,
      initDislikeCount: props.dislikeCount,
    };

    this.like = this.like.bind(this);
    this.dislike = this.dislike.bind(this);
  }

  componentWillReceiveProps(nextProps) {
    // jiri@visionapps.cz 30.05.2017 DBJR-1167
    // Preventing of repeated voting is implemented on backend.
    // This construction is only for informing user and it is not fully reliable in case of
    // parallel voting from more users at the same time
    if (this.state.expectLikesUpdate && !this.state.votingLimitErrorShowed
      && nextProps.likeCount === this.state.initLikeCount
      && nextProps.dislikeCount === this.state.initDislikeCount
    ) {
      this.setState({ votingLimitErrorShowed: true });
      alert(this.props.votingLimitError);
    }
  }

  like() {
    this.props.likeAction();
    this.setState({ voted: true, expectLikesUpdate: true });
  }

  dislike() {
    this.props.dislikeAction();
    this.setState({ voted: true, expectLikesUpdate: true });
  }

  render() {
    return (
      <div>
        <div style={{ display: 'inline-block' }} className="offset-bottom-small offset-right">
          <span className="badge offset-right-small">{this.props.likeCount}</span>
          <ThumbButton
            type="like"
            onTouchTap={this.like}
            disabled={this.state.voted}
            label={this.props.likeLabel}
          />
        </div>
        <div style={{ display: 'inline-block' }} className="offset-bottom-small">
          <span className="badge offset-right-small">{this.props.dislikeCount}</span>
          <ThumbButton
            type="dislike"
            onTouchTap={this.dislike}
            disabled={this.state.voted}
            label={this.props.dislikeLabel}
          />
        </div>
      </div>
    );
  }
}

Reputation.defaultProps = {
  voted: false,
};

Reputation.propTypes = {
  dislikeAction: React.PropTypes.func.isRequired,
  dislikeCount: React.PropTypes.number.isRequired,
  dislikeLabel: React.PropTypes.string.isRequired,
  likeAction: React.PropTypes.func.isRequired,
  likeCount: React.PropTypes.number.isRequired,
  likeLabel: React.PropTypes.string.isRequired,
  voted: React.PropTypes.bool,
  votingLimitError: React.PropTypes.string.isRequired,
};

export default Reputation;

import React from 'react';
import ThumbButton from '../../components/ThumbButton/ThumbButton';

/* global baseUrl */
/* global followupTranslations */

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
      && nextProps.dislikeCount === this.state.initDislikeCount) {
      this.setState({ votingLimitErrorShowed: true });
      alert(followupTranslations.votingLimitError);
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
        <span className="badge">{this.props.likeCount}</span>
        <ThumbButton type="like" onTouchTap={this.like} disabled={this.state.voted} />
        <span className="badge">{this.props.dislikeCount}</span>
        <ThumbButton type="dislike" onTouchTap={this.dislike} disabled={this.state.voted} />
      </div>
    );
  }
}

Reputation.defaultProps = {
  voted: false,
};

Reputation.propTypes = {
  likeCount: React.PropTypes.number.isRequired,
  dislikeCount: React.PropTypes.number.isRequired,
  likeAction: React.PropTypes.func.isRequired,
  dislikeAction: React.PropTypes.func.isRequired,
  voted: React.PropTypes.bool,
};

export default Reputation;

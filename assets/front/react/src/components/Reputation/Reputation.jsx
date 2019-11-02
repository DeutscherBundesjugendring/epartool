import React from 'react';
import PropTypes from 'prop-types';
import ThumbButton from '../ThumbButton/ThumbButton';

class Reputation extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      expectLikesUpdate: false,
      initLikeCount: props.likeCount,
      voted: props.voted,
      votingLimitErrorShowed: false,
    };

    this.like = this.like.bind(this);
    this.dislike = this.dislike.bind(this);
  }

  componentDidUpdate(nextProps) {
    // jiri@visionapps.cz 30.05.2017 DBJR-1167
    // Preventing of repeated voting is implemented on backend.
    // This construction is only for informing user and it is not fully reliable in case of
    // parallel voting from more users at the same time
    const {
      expectLikesUpdate,
      initDislikeCount,
      initLikeCount,
      votingLimitErrorShowed,
    } = this.state;
    const { votingLimitError } = this.props;

    if (expectLikesUpdate && !votingLimitErrorShowed
      && nextProps.likeCount === initLikeCount
      && nextProps.dislikeCount === initDislikeCount
    ) {
      // eslint-disable-next-line react/no-did-update-set-state
      this.setState({ votingLimitErrorShowed: true });
      alert(votingLimitError); // eslint-disable-line no-alert
    }
  }

  like() {
    const { likeAction } = this.props;
    likeAction();
    this.setState({
      expectLikesUpdate: true,
      voted: true,
    });
  }

  dislike() {
    const { dislikeAction } = this.props;
    dislikeAction();
    this.setState({
      expectLikesUpdate: true,
      voted: true,
    });
  }

  render() {
    const {
      dislikeLabel,
      dislikeCount,
      likeCount,
      likeLabel,
    } = this.props;

    const { voted } = this.state;
    return (
      <div>
        <div style={{ display: 'inline-block' }} className="offset-bottom-small offset-right">
          <span className="badge offset-right-small">{likeCount}</span>
          <ThumbButton
            type="like"
            onClick={this.like}
            disabled={voted}
            label={likeLabel}
          />
        </div>
        <div style={{ display: 'inline-block' }} className="offset-bottom-small">
          <span className="badge offset-right-small">{dislikeCount}</span>
          <ThumbButton
            type="dislike"
            onClick={this.dislike}
            disabled={voted}
            label={dislikeLabel}
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
  dislikeAction: PropTypes.func.isRequired,
  dislikeCount: PropTypes.number.isRequired,
  dislikeLabel: PropTypes.string.isRequired,
  likeAction: PropTypes.func.isRequired,
  likeCount: PropTypes.number.isRequired,
  likeLabel: PropTypes.string.isRequired,
  voted: PropTypes.bool,
  votingLimitError: PropTypes.string.isRequired,
};

export default Reputation;

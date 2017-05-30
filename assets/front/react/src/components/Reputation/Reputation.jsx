import React from 'react';
import ThumbButton from '../../components/ThumbButton/ThumbButton';

/* global baseUrl */
/* global followupTranslations */

class Reputation extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      voted: props.voted,
    };

    this.like = this.like.bind(this);
    this.dislike = this.dislike.bind(this);
  }

  like() {
    this.props.likeAction();
    this.setState({ voted: true });
  }

  dislike() {
    this.props.dislikeAction();
    this.setState({ voted: true });
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

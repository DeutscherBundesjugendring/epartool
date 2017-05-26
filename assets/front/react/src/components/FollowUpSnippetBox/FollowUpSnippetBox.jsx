import React from 'react';
import EmbeddedVideo from '../EmbeddedVideo/EmbeddedVideo';
import ThumbButton from '../ThumbButton/ThumbButton';
import RaisedButton from '../RaisedButton/RaisedButton';


const FollowUpSnippetBox = props => (
  <div className="well well-bordered followup-well followup-well-link">
    <p>
      {props.snippetExplanation}
    </p>
    <div className="offset-bottom-small">
      <span className="badge">{props.likeCount}</span>
      <ThumbButton type="like" onTouchTap={props.likeAction} />
      <span className="badge">{props.dislikeCount}</span>
      <ThumbButton type="dislike" onTouchTap={props.dislikeAction} />
    </div>
    <RaisedButton label={props.followPathLabel} onTouchTap={props.followPathAction} />
  </div>
);

FollowUpSnippetBox.propTypes = {
  snippetExplanation: React.PropTypes.string.isRequired,
  likeAction: React.PropTypes.func.isRequired,
  likeCount: React.PropTypes.number.isRequired,
  dislikeAction: React.PropTypes.func.isRequired,
  dislikeCount: React.PropTypes.number.isRequired,
  followPathAction: React.PropTypes.func.isRequired,
  followPathLabel: React.PropTypes.string.isRequired,
};

export default FollowUpSnippetBox;

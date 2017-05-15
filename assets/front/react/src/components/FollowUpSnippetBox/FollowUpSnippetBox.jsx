import React from 'react';
import ThumbButton from '../ThumbButton/ThumbButton';
import RaisedButton from '../RaisedButton/RaisedButton';


const FollowUpSnippetBox = props => (
  <div className="well well-bordered followup-well followup-well-link">
    {props.snippetImageSrc &&
      <img
        src={props.snippetImageSrc}
        alt="Snippet"
        width="80"
        className="followup-timeline-box-image"
      />
    }

    {props.snippet}

    <div className="offset-bottom-small">
      <span className="badge">{props.likeCount}</span>
      <ThumbButton type="like" onTouchTap={props.likeAction} />
      <span className="badge">{props.dislikeCount}</span>
      <ThumbButton type="dislike" onTouchTap={props.dislikeAction} />
    </div>

    <RaisedButton label={props.continueLabel} onTouchTap={props.continueAction} />
  </div>
);

FollowUpSnippetBox.propTypes = {
  snippet: React.PropTypes.string.isRequired,
  snippetImageSrc: React.PropTypes.string,
  likeAction: React.PropTypes.func.isRequired,
  likeCount: React.PropTypes.number.isRequired,
  dislikeAction: React.PropTypes.func.isRequired,
  dislikeCount: React.PropTypes.number.isRequired,
  continueAction: React.PropTypes.func.isRequired,
  continueLabel: React.PropTypes.string.isRequired,
};

export default FollowUpSnippetBox;

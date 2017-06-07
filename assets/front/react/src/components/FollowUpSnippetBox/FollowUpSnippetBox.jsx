import React from 'react';
import Reputation from '../Reputation/Reputation';
import RaisedButton from '../RaisedButton/RaisedButton';


const FollowUpSnippetBox = (props) => {
  let glypClasses = 'followup-type-icon glyphicon';
  let isType = false;
  let glypTitle = '';

  if (props.type === 'supporting') {
    glypClasses += ' glyphicon-heart';
    glypTitle = 'Supporting';
    isType = true;
  } else if (props.type === 'action') {
    glypClasses += ' glyphicon-play';
    glypTitle = 'Action';
    isType = true;
  } else if (props.type === 'rejected') {
    glypClasses += ' glyphicon-minus-sign';
    glypTitle = 'Rejected';
    isType = true;
  } else if (props.type === 'end') {
    glypClasses += ' glyphicon-lock';
    glypTitle = 'End';
    isType = true;
  }

  return (
    <div
      className="
        well
        well-bordered
        followup-flow
        followup-well
        followup-well-link
        followup-type-wrap
        followup-type-wrap-inner
      "
    >
      {isType && (
        <div className="followup-type followup-type-right followup-type-right-alt">
          <span className={glypClasses} aria-hidden="true" />
          <span className="followup-type-title">{glypTitle}</span>
        </div>
      )}
      <div dangerouslySetInnerHTML={{ __html: props.snippetExplanation }} />
      <div className="offset-bottom-small">
        <Reputation
          likeCount={props.likeCount}
          dislikeCount={props.dislikeCount}
          likeAction={props.likeAction}
          dislikeAction={props.dislikeAction}
        />
      </div>
      <RaisedButton label={props.followPathLabel} onTouchTap={props.followPathAction} />
    </div>
  );
};

FollowUpSnippetBox.propTypes = {
  type: React.PropTypes.string.isRequired,
  snippetExplanation: React.PropTypes.string.isRequired,
  likeAction: React.PropTypes.func.isRequired,
  likeCount: React.PropTypes.number.isRequired,
  dislikeAction: React.PropTypes.func.isRequired,
  dislikeCount: React.PropTypes.number.isRequired,
  followPathAction: React.PropTypes.func.isRequired,
  followPathLabel: React.PropTypes.string.isRequired,
};

export default FollowUpSnippetBox;

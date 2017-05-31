import React from 'react';
import Reputation from '../Reputation/Reputation';
import RaisedButton from '../RaisedButton/RaisedButton';


const FollowUpSnippetBox = (props) => {
  let glypClasses = 'followup-type-icon glyphicon';
  let isType = false;

  if (props.type === 'supporting') {
    glypClasses += ' glyphicon-heart';
    isType = true;
  } else if (props.type === 'action') {
    glypClasses += ' glyphicon-play';
    isType = true;
  } else if (props.type === 'rejected') {
    glypClasses += ' glyphicon-minus-sign';
    isType = true;
  } else if (props.type === 'end') {
    glypClasses += ' glyphicon-lock';
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

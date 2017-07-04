import React from 'react';
import Reputation from '../Reputation/Reputation';


const FollowUpSnippetBox = (props) => {
  let glypClasses = 'followup-type-icon glyphicon';
  let isType = false;
  let glypTitle = '';

  if (props.type === 'supporting') {
    glypClasses += ' glyphicon-heart';
    glypTitle = props.typeSupportingLabel;
    isType = true;
  } else if (props.type === 'action') {
    glypClasses += ' glyphicon-play';
    glypTitle = props.typeActionLabel;
    isType = true;
  } else if (props.type === 'rejected') {
    glypClasses += ' glyphicon-minus-sign';
    glypTitle = props.typeRejectedLabel;
    isType = true;
  } else if (props.type === 'end') {
    glypClasses += ' glyphicon-lock';
    glypTitle = props.typeEndLabel;
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
      <Reputation
        likeCount={props.likeCount}
        dislikeCount={props.dislikeCount}
        likeAction={props.likeAction}
        dislikeAction={props.dislikeAction}
      />
    </div>
  );
};

FollowUpSnippetBox.propTypes = {
  type: React.PropTypes.oneOf(['general', 'supporting', 'action', 'rejected', 'end']).isRequired,
  snippetExplanation: React.PropTypes.string.isRequired,
  likeAction: React.PropTypes.func.isRequired,
  likeCount: React.PropTypes.number.isRequired,
  dislikeAction: React.PropTypes.func.isRequired,
  dislikeCount: React.PropTypes.number.isRequired,
  typeActionLabel: React.PropTypes.string.isRequired,
  typeEndLabel: React.PropTypes.string.isRequired,
  typeRejectedLabel: React.PropTypes.string.isRequired,
  typeSupportingLabel: React.PropTypes.string.isRequired,
};

export default FollowUpSnippetBox;

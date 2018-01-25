import React from 'react';
import Reputation from '../Reputation/Reputation';


const FollowUpSnippetBox = (props) => {
  let glyphClasses = 'followup-type-icon glyphicon';
  let isType = false;
  let glyphTitle = '';

  if (props.type === 'supporting') {
    glyphClasses += ' glyphicon-heart';
    glyphTitle = props.typeSupportingLabel;
    isType = true;
  } else if (props.type === 'action') {
    glyphClasses += ' glyphicon-play';
    glyphTitle = props.typeActionLabel;
    isType = true;
  } else if (props.type === 'rejected') {
    glyphClasses += ' glyphicon-minus-sign';
    glyphTitle = props.typeRejectedLabel;
    isType = true;
  } else if (props.type === 'end') {
    glyphClasses += ' glyphicon-lock';
    glyphTitle = props.typeEndLabel;
    isType = true;
  }

  return (
    <div
      className="
        well
        well-bordered
        followup-flow
        followup-well
        followup-well-collapsible
        followup-well-link
        followup-type-wrap
        followup-type-wrap-inner
      "
    >
      <div className="js-followup-box-head">
        {isType && (
          <div className="followup-type followup-type-right followup-type-right-alt">
            <span className={glyphClasses} aria-hidden="true" />
            <span className="followup-type-title">{glyphTitle}</span>
          </div>
        )}
        <img
          src={props.document.previewImageLink}
          alt={props.document.title}
          width="120"
          className="offset-bottom img-responsive"
        />
      </div>
      <div
        className="js-followup-box-content"
        // eslint-disable-next-line react/no-danger
        dangerouslySetInnerHTML={{ __html: props.snippetExplanation }}
      />
      <div className="followup-reputation">
        <Reputation
          dislikeAction={props.dislikeAction}
          dislikeCount={props.dislikeCount}
          dislikeLabel={props.dislikeLabel}
          likeAction={props.likeAction}
          likeCount={props.likeCount}
          likeLabel={props.likeLabel}
          votingLimitError={props.votingLimitError}
        />
      </div>
    </div>
  );
};

FollowUpSnippetBox.propTypes = {
  type: React.PropTypes.oneOf(['general', 'supporting', 'action', 'rejected', 'end']).isRequired,
  snippetExplanation: React.PropTypes.string.isRequired,
  likeAction: React.PropTypes.func.isRequired,
  likeCount: React.PropTypes.number.isRequired,
  likeLabel: React.PropTypes.string.isRequired,
  dislikeAction: React.PropTypes.func.isRequired,
  dislikeCount: React.PropTypes.number.isRequired,
  dislikeLabel: React.PropTypes.string.isRequired,
  document: React.PropTypes.shape({
    previewImageLink: React.PropTypes.string.isRequired,
    title: React.PropTypes.string.isRequired,
  }).isRequired,
  typeActionLabel: React.PropTypes.string.isRequired,
  typeEndLabel: React.PropTypes.string.isRequired,
  typeRejectedLabel: React.PropTypes.string.isRequired,
  typeSupportingLabel: React.PropTypes.string.isRequired,
  votingLimitError: React.PropTypes.string.isRequired,
};

export default FollowUpSnippetBox;

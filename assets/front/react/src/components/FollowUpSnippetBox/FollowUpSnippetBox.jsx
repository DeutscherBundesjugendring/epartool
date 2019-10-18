import React from 'react';
import PropTypes from 'prop-types';
import Reputation from '../Reputation/Reputation';

const FollowUpSnippetBox = ({
  dislikeAction,
  dislikeCount,
  dislikeLabel,
  document,
  likeAction,
  likeCount,
  likeLabel,
  snippetExplanation,
  type,
  typeActionLabel,
  typeEndLabel,
  typeRejectedLabel,
  typeSupportingLabel,
  votingLimitError,
}) => {
  let glyphClasses = 'followup-type-icon glyphicon';
  let isType = false;
  let glyphTitle = '';

  if (type === 'supporting') {
    glyphClasses += ' glyphicon-heart';
    glyphTitle = typeSupportingLabel;
    isType = true;
  } else if (type === 'action') {
    glyphClasses += ' glyphicon-play';
    glyphTitle = typeActionLabel;
    isType = true;
  } else if (type === 'rejected') {
    glyphClasses += ' glyphicon-minus-sign';
    glyphTitle = typeRejectedLabel;
    isType = true;
  } else if (type === 'end') {
    glyphClasses += ' glyphicon-lock';
    glyphTitle = typeEndLabel;
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
          src={document.previewImageLink}
          alt={document.title}
          width="120"
          className="offset-bottom img-responsive"
        />
      </div>
      <div
        className="js-followup-box-content"
        // eslint-disable-next-line react/no-danger
        dangerouslySetInnerHTML={{ __html: snippetExplanation }}
      />
      <div className="followup-reputation">
        <Reputation
          dislikeAction={dislikeAction}
          dislikeCount={dislikeCount}
          dislikeLabel={dislikeLabel}
          likeAction={likeAction}
          likeCount={likeCount}
          likeLabel={likeLabel}
          votingLimitError={votingLimitError}
        />
      </div>
    </div>
  );
};

FollowUpSnippetBox.propTypes = {
  dislikeAction: PropTypes.func.isRequired,
  dislikeCount: PropTypes.number.isRequired,
  dislikeLabel: PropTypes.string.isRequired,
  document: PropTypes.shape({
    previewImageLink: PropTypes.string.isRequired,
    title: PropTypes.string.isRequired,
  }).isRequired,
  likeAction: PropTypes.func.isRequired,
  likeCount: PropTypes.number.isRequired,
  likeLabel: PropTypes.string.isRequired,
  snippetExplanation: PropTypes.string.isRequired,
  type: PropTypes.oneOf(['general', 'supporting', 'action', 'rejected', 'end']).isRequired,
  typeActionLabel: PropTypes.string.isRequired,
  typeEndLabel: PropTypes.string.isRequired,
  typeRejectedLabel: PropTypes.string.isRequired,
  typeSupportingLabel: PropTypes.string.isRequired,
  votingLimitError: PropTypes.string.isRequired,
};

export default FollowUpSnippetBox;

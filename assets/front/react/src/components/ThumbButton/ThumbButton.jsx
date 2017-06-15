import React from 'react';

/* global followupTranslations */

const ThumbButton = (props) => {
  const linkClasses = 'btn btn-default btn-xs';
  let iconClasses = 'glyphicon ';
  let label = followupTranslations.likeLabel;

  if (props.type === 'like') {
    iconClasses += ' glyphicon-thumbs-up';
  }

  if (props.type === 'dislike') {
    iconClasses += ' glyphicon-thumbs-down';
    label = followupTranslations.dislikeLabel;
  }

  return (
    <button
      onTouchTap={(e) => {
        e.stopPropagation();
        if (!props.disabled) {
          props.onTouchTap();
        }
      }}
      className={linkClasses}
      disabled={props.disabled}
    >
      <span className="text-center offset-top-small offset-right-small small">
        {label}
      </span>
      <span className={iconClasses} aria-hidden="true" />
    </button>
  );
};

ThumbButton.defaultProps = {
  disabled: false,
};

ThumbButton.propTypes = {
  type: React.PropTypes.oneOf(['like', 'dislike']).isRequired,
  onTouchTap: React.PropTypes.func.isRequired,
  disabled: React.PropTypes.bool,
};


export default ThumbButton;

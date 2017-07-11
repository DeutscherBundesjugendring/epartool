import React from 'react';

const ThumbButton = (props) => {
  const linkClasses = 'btn btn-default btn-xs';
  let iconClasses = 'glyphicon ';

  if (props.type === 'like') {
    iconClasses += ' glyphicon-thumbs-up';
  } else if (props.type === 'dislike') {
    iconClasses += ' glyphicon-thumbs-down';
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
        {props.label}
      </span>
      <span className={iconClasses} aria-hidden="true" />
    </button>
  );
};

ThumbButton.defaultProps = {
  disabled: false,
};

ThumbButton.propTypes = {
  disabled: React.PropTypes.bool,
  label: React.PropTypes.string.isRequired,
  onTouchTap: React.PropTypes.func.isRequired,
  type: React.PropTypes.oneOf(['like', 'dislike']).isRequired,
};


export default ThumbButton;

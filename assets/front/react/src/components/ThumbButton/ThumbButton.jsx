import React from 'react';
import PropTypes from 'prop-types';

const ThumbButton = ({
  disabled,
  label,
  onClick,
  type,
}) => {
  const linkClasses = 'btn btn-default btn-xs';
  let iconClasses = 'glyphicon ';

  if (type === 'like') {
    iconClasses += ' glyphicon-thumbs-up';
  } else if (type === 'dislike') {
    iconClasses += ' glyphicon-thumbs-down';
  }

  return (
    <button
      type="button"
      onClick={(e) => {
        e.stopPropagation();
        if (!disabled) {
          onClick();
        }
      }}
      className={linkClasses}
      disabled={disabled}
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
  disabled: PropTypes.bool,
  label: PropTypes.string.isRequired,
  onClick: PropTypes.func.isRequired,
  type: PropTypes.oneOf(['like', 'dislike']).isRequired,
};

export default ThumbButton;

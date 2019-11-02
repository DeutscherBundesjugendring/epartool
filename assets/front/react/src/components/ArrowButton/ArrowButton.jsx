import React from 'react';
import PropTypes from 'prop-types';

const ArrowButton = (props) => {
  const {
    direction,
    disabled,
    label,
  } = props;

  let classes = 'followup-link followup-timeline-count followup-sprite followup-sprite-timeline-count';

  if (direction === 'inward') {
    classes += ' followup-timeline-count-arrow followup-timeline-count-arrow-inward';
  } else {
    classes += ' followup-timeline-count-arrow followup-timeline-count-arrow-outward';
  }

  return (
    <button
      onClick={(e) => {
        e.stopPropagation();
        props.onClick();
      }}
      disabled={disabled}
      className={classes}
      type="button"
    >
      {label}
    </button>
  );
};

ArrowButton.defaultProps = {
  disabled: false,
};

ArrowButton.propTypes = {
  direction: PropTypes.oneOf(['inward', 'outward']).isRequired,
  disabled: PropTypes.bool,
  label: PropTypes.string.isRequired,
  onClick: PropTypes.func.isRequired,
};

export default ArrowButton;

import React from 'react';


const ArrowButton = (props) => {
  let classes = 'followup-link followup-timeline-count followup-sprite followup-sprite-timeline-count';

  if (props.direction === 'inward') {
    classes += ' followup-timeline-count-arrow followup-timeline-count-arrow-inward';
  } else {
    classes += ' followup-timeline-count-arrow followup-timeline-count-arrow-outward';
  }

  return (
    <button
      onTouchTap={(e) => {
        e.stopPropagation();
        props.onTouchTap();
      }}
      disabled={props.disabled}
      className={classes}
    >
      {props.label}
    </button>
  );
};

ArrowButton.defaultProps = {
  disabled: false,
};

ArrowButton.propTypes = {
  label: React.PropTypes.string.isRequired,
  direction: React.PropTypes.oneOf(['inward', 'outward']).isRequired,
  onTouchTap: React.PropTypes.func.isRequired,
  disabled: React.PropTypes.bool,
};

export default ArrowButton;

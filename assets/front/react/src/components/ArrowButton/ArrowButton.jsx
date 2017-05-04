import React from 'react';


const ArrowButton = (props) => {
  let classes = 'followup-link followup-timeline-count followup-sprite followup-sprite-timeline-count';

  if (props.direction === 'left') {
    // Bedřich Schindler <bedrich@visionapps.cz>, 28. 4. 2017 16:00
    // TODO: Add css class followup-link-right
    classes += ' followup-link-left';
  }

  return (
    <button onTouchTap={props.onTouchTap} disabled={props.disabled} className={classes}>
      {props.label}
    </button>
  );
};

ArrowButton.defaultProps = {
  disabled: false,
};

ArrowButton.propTypes = {
  label: React.PropTypes.string.isRequired,
  direction: React.PropTypes.oneOf(['left', 'right']).isRequired,
  onTouchTap: React.PropTypes.func.isRequired,
  disabled: React.PropTypes.bool,
};

export default ArrowButton;

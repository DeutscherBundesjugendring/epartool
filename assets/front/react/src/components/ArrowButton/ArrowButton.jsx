import React from 'react';


const ArrowButton = (props) => {
  let classes = null;

  if (props.type === 'followup-timeline-count') {
    classes = 'followup-link followup-timeline-count followup-sprite followup-sprite-timeline-count';
  }

  if (props.direction === 'left') {
    // Bedřich Schindler <bedrich@visionapps.cz>, 28. 4. 2017 16:00
    // TODO: Add css class followup-link-right
    classes += ' followup-link-left';
  }

  return (
    <span className={classes}>
      {props.label}
    </span>
  );
};

ArrowButton.propTypes = {
  label: React.PropTypes.string.isRequired,
  type: React.PropTypes.oneOf(['followup-timeline-count']).isRequired,
  direction: React.PropTypes.oneOf(['left', 'right']).isRequired,

};


export default ArrowButton;

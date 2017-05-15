import React from 'react';
import ArrowButton from '../ArrowButton/ArrowButton';


const FollowUpBox = props => (
  <div className="followup-timeline-box" onTouchTap={props.modalAction}>
    {!!props.parentCount && <ArrowButton
      direction="left"
      label={props.parentCount.toString()}
      onTouchTap={props.parentAction}
    />}
    {props.element}
    {!!props.childCount && <ArrowButton
      direction="right"
      label={props.childCount.toString()}
      onTouchTap={props.childAction}
    />}
  </div>
);

FollowUpBox.propTypes = {
  element: React.PropTypes.element.isRequired,
  parentCount: React.PropTypes.number.isRequired,
  parentAction: React.PropTypes.func.isRequired,
  childCount: React.PropTypes.number.isRequired,
  childAction: React.PropTypes.func.isRequired,
  modalAction: React.PropTypes.func,
};

export default FollowUpBox;

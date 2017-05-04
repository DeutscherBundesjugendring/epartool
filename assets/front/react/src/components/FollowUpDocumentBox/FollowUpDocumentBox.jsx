import React from 'react';


const FollowDocumentBox = props => (
  <div className="followup-timeline-box" onTouchTap={props.modalAction}>
    <div className="well well-bordered followup-well followup-well-link">
      {props.document}
    </div>
  </div>
);

FollowDocumentBox.propTypes = {
  document: React.PropTypes.string.isRequired,
  modalAction: React.PropTypes.func.isRequired,
};

export default FollowDocumentBox;

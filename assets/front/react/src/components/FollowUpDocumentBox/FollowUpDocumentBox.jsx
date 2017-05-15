import React from 'react';


const FollowDocumentBox = props => (
  <div className="well well-bordered followup-well followup-well-link">
    {props.document}
  </div>
);

FollowDocumentBox.propTypes = {
  document: React.PropTypes.string.isRequired,
};

export default FollowDocumentBox;

import React from 'react';
import RaisedButton from '../RaisedButton/RaisedButton';


const FollowDocumentBox = props => (
  <div className="well well-bordered followup-well followup-well-link">
    <p>
      {props.title}
    </p>
    <p>
      {props.author}
    </p>
    <img
      src={props.previewImageLink}
      alt={props.title}
      width="80"
      className="followup-timeline-box-image"
    />
    {props.description}
    <RaisedButton label={props.downloadLabel} onTouchTap={props.downloadAction} />
  </div>
);

FollowDocumentBox.propTypes = {
  title: React.PropTypes.string.isRequired,
  author: React.PropTypes.string.isRequired,
  description: React.PropTypes.string.isRequired,
  previewImageLink: React.PropTypes.string.isRequired,
  downloadAction: React.PropTypes.func.isRequired,
  downloadLabel: React.PropTypes.string.isRequired,
};

export default FollowDocumentBox;

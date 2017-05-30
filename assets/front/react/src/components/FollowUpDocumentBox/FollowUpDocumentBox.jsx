import React from 'react';
import moment from 'moment';
import RaisedButton from '../RaisedButton/RaisedButton';


const FollowDocumentBox = props => (
  <div className="well well-bordered followup-well followup-well-link">
    <p>
      {props.title}
    </p>
    <p>
      {props.author}
    </p>
    <p>
      {props.dateMonthYearOnly
        ? moment(props.date).format('MMMM YYYY')
        : moment(props.date).format('D MMMM YYYY')
      }
    </p>
    <span>{props.type}</span>
    <img
      src={props.previewImageLink}
      alt={props.title}
      width="80"
      className="followup-timeline-box-image"
    />
    <div dangerouslySetInnerHTML={{ __html: props.description }} />
    <RaisedButton label={props.downloadLabel} onTouchTap={props.downloadAction} />
  </div>
);

FollowDocumentBox.propTypes = {
  type: React.PropTypes.string.isRequired,
  title: React.PropTypes.string.isRequired,
  author: React.PropTypes.string.isRequired,
  description: React.PropTypes.string.isRequired,
  date: React.PropTypes.objectOf(Date).isRequired,
  dateMonthYearOnly: React.PropTypes.bool.isRequired,
  previewImageLink: React.PropTypes.string.isRequired,
  downloadAction: React.PropTypes.func.isRequired,
  downloadLabel: React.PropTypes.string.isRequired,
};

export default FollowDocumentBox;

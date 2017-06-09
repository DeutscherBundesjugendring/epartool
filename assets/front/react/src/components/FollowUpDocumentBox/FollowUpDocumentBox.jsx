import React from 'react';
import moment from 'moment';
import RaisedButton from '../RaisedButton/RaisedButton';


const FollowDocumentBox = (props) => {
  let glypClasses = 'followup-type-icon glyphicon';
  let isType = false;
  let glypTitle = '';

  if (props.type === 'supporting') {
    glypClasses += ' glyphicon-heart';
    glypTitle = props.typeSupportingLabel;
    isType = true;
  } else if (props.type === 'action') {
    glypClasses += ' glyphicon-play';
    glypTitle = props.typeActionLabel;
    isType = true;
  } else if (props.type === 'rejected') {
    glypClasses += ' glyphicon-minus-sign';
    glypTitle = props.typeRejectedLabel;
    isType = true;
  } else if (props.type === 'end') {
    glypClasses += ' glyphicon-lock';
    glypTitle = props.typeEndLabel;
    isType = true;
  }

  return (
    <div
      className="
        well
        well-bordered
        followup-flow
        followup-well
        followup-well-link
        followup-type-wrap
        followup-type-wrap-inner
      "
    >
      {isType && (
        <div className="followup-type followup-type-right followup-type-right-alt">
          <span className={glypClasses} aria-hidden="true" />
          <span className="followup-type-title">{glypTitle}</span>
        </div>
      )}
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
      <img
        src={props.previewImageLink}
        alt={props.title}
        width="80"
        className="offset-bottom img-responsive"
      />
      <div className="offset-bottom" dangerouslySetInnerHTML={{ __html: props.description }} />
      <RaisedButton label={props.downloadLabel} onTouchTap={props.downloadAction} />
    </div>
  );
};

FollowDocumentBox.propTypes = {
  type: React.PropTypes.oneOf(['general', 'supporting', 'action', 'rejected', 'end']).isRequired,
  title: React.PropTypes.string.isRequired,
  author: React.PropTypes.string.isRequired,
  description: React.PropTypes.string.isRequired,
  date: React.PropTypes.objectOf(Date).isRequired,
  dateMonthYearOnly: React.PropTypes.bool.isRequired,
  previewImageLink: React.PropTypes.string.isRequired,
  downloadAction: React.PropTypes.func.isRequired,
  downloadLabel: React.PropTypes.string.isRequired,
  typeActionLabel: React.PropTypes.string.isRequired,
  typeEndLabel: React.PropTypes.string.isRequired,
  typeRejectedLabel: React.PropTypes.string.isRequired,
  typeSupportingLabel: React.PropTypes.string.isRequired,
};

export default FollowDocumentBox;

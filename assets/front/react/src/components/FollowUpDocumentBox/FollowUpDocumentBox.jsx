import React from 'react';
import PropTypes from 'prop-types';
import moment from 'moment';

const FollowDocumentBox = ({
  author,
  date,
  dateMonthYearOnly,
  description,
  previewImageLink,
  title,
  type,
  typeActionLabel,
  typeEndLabel,
  typeRejectedLabel,
  typeSupportingLabel,
}) => {
  let glypClasses = 'followup-type-icon glyphicon';
  let isType = false;
  let glypTitle = '';

  if (type === 'supporting') {
    glypClasses += ' glyphicon-heart';
    glypTitle = typeSupportingLabel;
    isType = true;
  } else if (type === 'action') {
    glypClasses += ' glyphicon-play';
    glypTitle = typeActionLabel;
    isType = true;
  } else if (type === 'rejected') {
    glypClasses += ' glyphicon-minus-sign';
    glypTitle = typeRejectedLabel;
    isType = true;
  } else if (type === 'end') {
    glypClasses += ' glyphicon-lock';
    glypTitle = typeEndLabel;
    isType = true;
  }

  return (
    <div
      className="
        well
        well-bordered
        followup-flow
        followup-well
        followup-well-collapsible
        followup-well-link
        followup-type-wrap
        followup-type-wrap-inner
      "
    >
      <div className="js-followup-box-head">
        {isType && (
          <div className="followup-type followup-type-right followup-type-right-alt">
            <span className={glypClasses} aria-hidden="true" />
            <span className="followup-type-title">{glypTitle}</span>
          </div>
        )}
        <h2 className="h4">{title}</h2>
        <p className="text-muted">
          {dateMonthYearOnly
            ? moment(date).format('MMMM YYYY')
            : moment(date).format('D MMMM YYYY')}
          <span className="offset-left offset-right">|</span>
          {author}
        </p>
        <img
          src={previewImageLink}
          alt={title}
          width="120"
          className="offset-bottom img-responsive"
        />
      </div>
      <div
        className="js-followup-box-content"
        // eslint-disable-next-line react/no-danger
        dangerouslySetInnerHTML={{ __html: description }}
      />
    </div>
  );
};

FollowDocumentBox.propTypes = {
  author: PropTypes.string.isRequired,
  date: PropTypes.objectOf(Date).isRequired,
  dateMonthYearOnly: PropTypes.bool.isRequired,
  description: PropTypes.string.isRequired,
  previewImageLink: PropTypes.string.isRequired,
  title: PropTypes.string.isRequired,
  type: PropTypes.oneOf(['general', 'supporting', 'action', 'rejected', 'end']).isRequired,
  typeActionLabel: PropTypes.string.isRequired,
  typeEndLabel: PropTypes.string.isRequired,
  typeRejectedLabel: PropTypes.string.isRequired,
  typeSupportingLabel: PropTypes.string.isRequired,
};

export default FollowDocumentBox;

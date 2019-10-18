import React from 'react';
import PropTypes from 'prop-types';
import moment from 'moment';
import EmbeddedVideo from '../EmbeddedVideo/EmbeddedVideo';
import Reputation from '../Reputation/Reputation';
import RaisedButton from '../RaisedButton/RaisedButton';

const FollowUpDocumentModal = ({
  author,
  closeAction,
  date,
  dateMonthYearOnly,
  downloadAction,
  downloadLabel,
  previewImageLink,
  snippets,
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
    <div>
      <div className="modal-backdrop fade in" />
      <div
        className="modal text-left fade in"
        aria-hidden="true"
        aria-labelledby="modalFollowupLabel"
        style={{ display: 'block' }}
      >
        <div className="modal-dialog">
          <div className="modal-content">

            <div className="modal-header modal-header-jumbo">

              <button
                type="button"
                className="close close-inverse"
                aria-label="Close"
                onClick={closeAction}
              >
                <span aria-hidden="true">×</span>
              </button>

              <div className="followup-type-wrap">
                {isType && (
                <div className="followup-type">
                  <span className={glypClasses} aria-hidden="true" />
                  <span className="followup-type-title">{glypTitle}</span>
                </div>
                )}
                <div className="row">
                  <div className="col-sm-4">

                    <img
                      src={previewImageLink}
                      width={256}
                      height={160}
                      className="followup-document-image img-responsive offset-bottom-xs-max"
                      alt={title}
                    />

                  </div>
                  <div className="col-sm-7">

                    <h2 id="modalFollowupLabel" className="modal-title offset-bottom-small">
                      {title}
                    </h2>
                    <p className="small">
                      {dateMonthYearOnly
                        ? moment(date).format('M. YYYY')
                        : moment(date).format('D. M. YYYY')}
                      <span className="offset-left offset-right">|</span>
                      {author}
                    </p>
                    <button
                      className="btn btn-default btn-default-alt btn-sm"
                      type="button"
                      onClick={downloadAction}
                    >
                      <span className="glyphicon glyphicon-file icon-offset" />
                      {downloadLabel}
                    </button>

                  </div>
                </div>
              </div>

            </div>
            <div className="modal-body">

              {!!snippets && snippets.map((snippet, index) => (
                <div
                  className="well well-simple well-simple-light"
                  // eslint-disable-next-line react/no-array-index-key
                  key={index}
                >
                  {!!snippet.videoService && !!snippet.videoId
                  && (
                  <EmbeddedVideo
                    videoService={snippet.videoService}
                    videoId={snippet.videoId}
                  />
                  )}

                  <div
                  // eslint-disable-next-line react/no-danger
                    dangerouslySetInnerHTML={{ __html: snippet.snippetExplanation }}
                  />

                  <div className="offset-top-small offset-bottom-small">
                    <div className="row">
                      <div className="col-sm-7 text-left">
                        <div className="offset-top-small">
                          <Reputation
                            likeCount={snippet.likeCount}
                            dislikeCount={snippet.dislikeCount}
                            likeLabel={snippet.likeLabel}
                            likeAction={snippet.likeAction}
                            dislikeAction={snippet.dislikeAction}
                            dislikeLabel={snippet.dislikeLabel}
                            votingLimitError={snippet.votingLimitError}
                          />
                        </div>
                      </div>
                      <div className="col-sm-5 text-right">
                        {snippet.showFollowPathButton
                        && (
                        <div className="offset-top-small">
                          <RaisedButton
                            label={snippet.followPathLabel}
                            onClick={snippet.followPathAction}
                          />
                        </div>
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

FollowUpDocumentModal.defaultProps = {
  snippets: [],
};

FollowUpDocumentModal.propTypes = {
  author: PropTypes.string.isRequired,
  closeAction: PropTypes.func.isRequired,
  date: PropTypes.objectOf(Date).isRequired,
  dateMonthYearOnly: PropTypes.bool.isRequired,
  downloadAction: PropTypes.func.isRequired,
  downloadLabel: PropTypes.string.isRequired,
  previewImageLink: PropTypes.string.isRequired,
  snippets: PropTypes.arrayOf(PropTypes.shape({
    dislikeAction: PropTypes.func.isRequired,
    dislikeCount: PropTypes.number.isRequired,
    dislikeLabel: PropTypes.string.isRequired,
    followPathAction: PropTypes.func.isRequired,
    followPathLabel: PropTypes.string.isRequired,
    likeAction: PropTypes.func.isRequired,
    likeCount: PropTypes.number.isRequired,
    likeLabel: PropTypes.string.isRequired,
    showFollowPathButton: PropTypes.bool.isRequired,
    snippetExplanation: PropTypes.string.isRequired,
    votingLimitError: PropTypes.string.isRequired,
  })),
  title: PropTypes.string.isRequired,
  type: PropTypes.oneOf(['general', 'supporting', 'action', 'rejected', 'end']).isRequired,
  typeActionLabel: PropTypes.string.isRequired,
  typeEndLabel: PropTypes.string.isRequired,
  typeRejectedLabel: PropTypes.string.isRequired,
  typeSupportingLabel: PropTypes.string.isRequired,
};

export default FollowUpDocumentModal;

import React from 'react';
import moment from 'moment';
import EmbeddedVideo from '../EmbeddedVideo/EmbeddedVideo';
import Reputation from '../Reputation/Reputation';
import RaisedButton from '../RaisedButton/RaisedButton';


const FollowUpDocumentModal = (props) => {
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

  return (<div>
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
              onTouchTap={props.closeAction}
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
                    src={props.previewImageLink}
                    width={256}
                    height={160}
                    className="followup-document-image img-responsive offset-bottom-xs-max"
                    alt={props.title}
                  />

                </div>
                <div className="col-sm-7">

                  <h2 id="modalFollowupLabel" className="modal-title offset-bottom-small">
                    {props.title}
                  </h2>
                  <p className="small">
                    {props.dateMonthYearOnly
                      ? moment(props.date).format('M. YYYY')
                      : moment(props.date).format('D. M. YYYY')
                    }
                    <span className="offset-left offset-right">|</span>
                    {props.author}
                  </p>
                  <button
                    className="btn btn-default btn-default-alt btn-sm"
                    onTouchTap={props.downloadAction}
                  >
                    <span className="glyphicon glyphicon-file icon-offset" />
                    {props.downloadLabel}
                  </button>

                </div>
              </div>
            </div>

          </div>
          <div className="modal-body">

            {!!props.snippets && props.snippets.map((snippet, index) => (
              <div
                className="well well-simple well-simple-light"
                key={index}
              >
                {!!snippet.videoService && !!snippet.videoId &&
                  <EmbeddedVideo
                    videoService={snippet.videoService}
                    videoId={snippet.videoId}
                  />
                }

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
                      {snippet.showFollowPathButton &&
                        <div className="offset-top-small">
                          <RaisedButton
                            label={snippet.followPathLabel}
                            onTouchTap={snippet.followPathAction}
                          />
                        </div>
                      }
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

FollowUpDocumentModal.propTypes = {
  type: React.PropTypes.oneOf(['general', 'supporting', 'action', 'rejected', 'end']).isRequired,
  title: React.PropTypes.string.isRequired,
  author: React.PropTypes.string.isRequired,
  date: React.PropTypes.objectOf(Date).isRequired,
  dateMonthYearOnly: React.PropTypes.bool.isRequired,
  previewImageLink: React.PropTypes.string.isRequired,
  closeAction: React.PropTypes.func.isRequired,
  downloadAction: React.PropTypes.func.isRequired,
  downloadLabel: React.PropTypes.string.isRequired,
  typeActionLabel: React.PropTypes.string.isRequired,
  typeEndLabel: React.PropTypes.string.isRequired,
  typeRejectedLabel: React.PropTypes.string.isRequired,
  typeSupportingLabel: React.PropTypes.string.isRequired,
  snippets: React.PropTypes.arrayOf(React.PropTypes.shape({
    snippetExplanation: React.PropTypes.string.isRequired,
    likeAction: React.PropTypes.func.isRequired,
    likeCount: React.PropTypes.number.isRequired,
    likeLabel: React.PropTypes.string.isRequired,
    dislikeAction: React.PropTypes.func.isRequired,
    dislikeCount: React.PropTypes.number.isRequired,
    dislikeLabel: React.PropTypes.string.isRequired,
    followPathAction: React.PropTypes.func.isRequired,
    followPathLabel: React.PropTypes.string.isRequired,
    showFollowPathButton: React.PropTypes.bool.isRequired,
    votingLimitError: React.PropTypes.string.isRequired,
  })),
};

export default FollowUpDocumentModal;

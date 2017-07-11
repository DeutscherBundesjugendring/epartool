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
      className="modal fade in"
      aria-hidden="true"
      aria-labelledby="modalFollowupLabel"
      style={{ display: 'block' }}
    >
      <div className="modal-dialog">
        <div className="modal-content">

          <div className="modal-header">
            <button
              type="button"
              className="close"
              aria-label="Close"
              onTouchTap={props.closeAction}
            >
              <span aria-hidden="true">×</span>
            </button>
            <h4 className="modal-title" id="modalFollowupLabel">
              Document
            </h4>
          </div>
          <div className="modal-body">

            <div className="well well-accent followup-type-wrap">
              {isType && (
                <div className="followup-type followup-type-right">
                  <span className={glypClasses} aria-hidden="true" />
                  <span className="followup-type-title">{glypTitle}</span>
                </div>
              )}
              <div className="row">
                <div className="col-sm-4 text-center">
                  <img
                    className="img-responsive center-block offset-bottom"
                    src={props.previewImageLink}
                    alt={props.title}
                  />
                </div>
                <div className="col-sm-8">
                  <h4 className="well-title well-title-small">{props.title}</h4>
                  <p className="small">
                    {props.dateMonthYearOnly
                      ? moment(props.date).format('MMMM YYYY')
                      : moment(props.date).format('D MMMM YYYY')
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

            <hr />

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

                <div dangerouslySetInnerHTML={{ __html: snippet.snippetExplanation }} />

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

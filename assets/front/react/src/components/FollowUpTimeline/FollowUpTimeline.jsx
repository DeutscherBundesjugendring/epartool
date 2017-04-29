import React from 'react';


const FollowUpTimeLine = props => (
  <div className="well well-bordered well-accent">

    <div className="row offset-bottom-large">
      <div className="col-sm-4">
        <a
          href={props.infoLink}
          title={props.infoLinkTitle}
          className="followup-back-link followup-link media link-unstyled link-unstyled-alt"
        >
          <div className="media-left">
            <div
              className={
                'followup-back-link-question-mark followup-sprite followup-sprite-question-mark'
              }
              aria-hidden="true"
            >
              ?
            </div>
          </div>
          <div className="media-body">
            <strong className="small">
              {props.infoText}
            </strong>
          </div>
        </a>

      </div>
    </div>

    <div className="followup">
      <div className="row">
        <div className="col-sm-4">
          {props.leftColumn && props.leftColumn.map((element, index) => (
            <div key={index}>
              {element}
            </div>
          ))}
        </div>
        <div className="col-sm-4">
          {props.centerColumn && props.centerColumn.map((element, index) => (
            <div key={index}>
              {element}
            </div>
          ))}
        </div>
        <div className="col-sm-4">
          {props.rightColumn && props.rightColumn.map((element, index) => (
            <div key={index}>
              {element}
            </div>
          ))}
        </div>
      </div>
    </div>

  </div>
);

FollowUpTimeLine.propTypes = {
  infoLink: React.PropTypes.string.isRequired,
  infoLinkTitle: React.PropTypes.string.isRequired,
  infoText: React.PropTypes.string.isRequired,
  leftColumn: React.PropTypes.arrayOf(React.PropTypes.element),
  centerColumn: React.PropTypes.arrayOf(React.PropTypes.element),
  rightColumn: React.PropTypes.arrayOf(React.PropTypes.element),
};

export default FollowUpTimeLine;

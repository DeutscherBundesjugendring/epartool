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
      {props.columns && props.columns.map((elements, columnKey) => (
        <div className="followup-column" key={columnKey}>
          {elements && elements.map((element, elementKey) => (
            <div className="followup-column-element" key={elementKey}>
              {element}
            </div>
          ))}
        </div>
      ))}
    </div>

  </div>
);

FollowUpTimeLine.propTypes = {
  infoLink: React.PropTypes.string.isRequired,
  infoLinkTitle: React.PropTypes.string.isRequired,
  infoText: React.PropTypes.string.isRequired,
  columns: React.PropTypes.arrayOf(React.PropTypes.arrayOf(React.PropTypes.element)),
};

export default FollowUpTimeLine;

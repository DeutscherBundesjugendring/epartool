import React from 'react';


const FollowUpTimeLine = props => (
  <div
    className={`
      well
      well-bordered
      well-accent
      followup-stage
      ${props.isLoading && 'followup-stage-loading'}
    `}
  >

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
      {props.isInitialLoad && (<div>
        <div className="followup-rows-container">
          <div className="followup-row">
            <div className="followup-row-element">
              <div
                className="
                  well
                  well-bordered
                  well-placeholder
                  well-placeholder-small
                  followup-placeholder
                "
              />
            </div>
            <div className="followup-row-element">
              <div
                className="
                  well
                  well-bordered
                  well-placeholder
                  followup-placeholder
                  followup-placeholder-delay-1
                "
              />
            </div>
            <div className="followup-row-element">
              <div
                className="
                  well
                  well-bordered
                  well-placeholder
                  followup-placeholder
                  followup-placeholder-delay-2
                "
              />
            </div>
          </div>
        </div>
      </div>)}
      <div className="followup-rows-container">
        {props.rows && props.rows.map((elements, rowKey) => (
          <div id={`row${rowKey}`} className="followup-row" key={rowKey}>
            {elements && elements.map((element, elementKey) => (
              <div
                className="followup-row-element"
                key={elementKey}
              >
                {element}
              </div>
            ))}
          </div>
        ))}
      </div>
    </div>

    {props.modal}

  </div>
);

FollowUpTimeLine.propTypes = {
  infoLink: React.PropTypes.string.isRequired,
  infoLinkTitle: React.PropTypes.string.isRequired,
  infoText: React.PropTypes.string.isRequired,
  isInitialLoad: React.PropTypes.bool,
  isLoading: React.PropTypes.bool,
  rows: React.PropTypes.arrayOf(React.PropTypes.arrayOf(React.PropTypes.element)),
  modal: React.PropTypes.element,
};

export default FollowUpTimeLine;

import React from 'react';
import PropTypes from 'prop-types';

const FollowUpTimeLine = ({
  infoLink,
  infoText,
  infoLinkTitle,
  isInitialLoad,
  isLoading,
  modal,
  rows,
}) => (
  <div
    className={`
      well
      well-bordered
      well-accent
      followup-stage
      ${isLoading && 'followup-stage-loading'}
    `}
  >

    <div className="row offset-bottom-large">
      <div className="col-sm-4">
        <a
          href={infoLink}
          title={infoLinkTitle}
          className="followup-back-link followup-link media link-unstyled link-unstyled-alt"
        >
          <div className="media-left">
            <div
              className="followup-back-link-question-mark followup-sprite followup-sprite-question-mark"
              aria-hidden="true"
            >
              ?
            </div>
          </div>
          <div className="media-body">
            <strong className="small">{infoText}</strong>
          </div>
        </a>

      </div>
    </div>

    <div className="followup">
      {isInitialLoad && (
      <div>
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
      </div>
      )}
      <div className="followup-rows-container">
        {rows && rows.map((elements, rowKey) => (
          // eslint-disable-next-line react/no-array-index-key
          <div id={`row${rowKey}`} className="followup-row" key={rowKey}>
            {elements && elements.map((element, elementKey) => (
              // eslint-disable-next-line react/no-array-index-key
              <div className="followup-row-element" key={elementKey}>
                {element}
              </div>
            ))}
          </div>
        ))}
      </div>
    </div>

    {modal}

  </div>
);

FollowUpTimeLine.defaultProps = {
  isInitialLoad: false,
  isLoading: false,
  modal: null,
  rows: [],
};

FollowUpTimeLine.propTypes = {
  infoLink: PropTypes.string.isRequired,
  infoLinkTitle: PropTypes.string.isRequired,
  infoText: PropTypes.string.isRequired,
  isInitialLoad: PropTypes.bool,
  isLoading: PropTypes.bool,
  modal: PropTypes.element,
  rows: PropTypes.arrayOf(PropTypes.arrayOf(PropTypes.element)),
};

export default FollowUpTimeLine;

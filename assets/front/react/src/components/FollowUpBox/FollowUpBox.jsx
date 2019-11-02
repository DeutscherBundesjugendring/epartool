import React from 'react';
import PropTypes from 'prop-types';
import ArrowButton from '../ArrowButton/ArrowButton';
import RaisedButton from '../RaisedButton/RaisedButton';

class FollowUpBox extends React.Component {
  componentDidMount() {
    this.adjustContributionHeight();
    this.addToggle();
  }

  componentDidUpdate() {
    this.adjustContributionHeight();
    this.addToggle();
  }

  adjustContributionHeight() {
    /*
    Makes second div in contributionBox shrink to accommodate first div
    Lives in this component instead of contributionBox b/c of forceUpdate
    Operating on DOM b/c needs to happen after render
    */
    const {
      id,
      isOpened,
      type,
    } = this.props;

    const wellArray = window.document
      .getElementById(`${type}-${id}`)
      .getElementsByClassName('followup-flow')[0];
    if (type === 'contribution') {
      // 400px is collapsed followup-timeline-box height, -20px is adjustment for margin
      const newHeight = 400
        - (wellArray.getElementsByClassName('js-followup-box-head')[0].offsetHeight - 20);
      wellArray.getElementsByClassName('js-followup-box-content')[0].style.height = isOpened() ? '' : `${newHeight}px`;
    } else {
      const headEl = wellArray.getElementsByClassName('js-followup-box-head')[0];
      // 400px is collapsed followup-timeline-box height, -20px is adjustment for margin
      // 31.01.2018 - jiri@visionapps.cz - 121px is an experimentally found value to prevent two
      // phase resizing of the box when it is collapsing
      const newHeight = 400 - headEl.offsetHeight - 121;
      wellArray.getElementsByClassName('js-followup-box-content')[0].style.height = isOpened()
        ? ''
        : `${newHeight}px`;
    }
  }

  addToggle() {
    /*
    Checks for overflow of box
    Operating on DOM b/c needs to happen after render
    */
    const {
      id,
      type,
    } = this.props;

    const box = window.document.getElementById(`${type}-${id}`);
    let element = box.getElementsByClassName('followup-flow')[0];

    if (type === 'contribution') {
      // eslint-disable-next-line prefer-destructuring
      element = element.getElementsByTagName('div')[1];
    }

    const canOverflow = element.scrollHeight > element.clientHeight;
    if (canOverflow) {
      window.document.getElementById(`${type}-${id}-toggle`).style.display = '';
    } else {
      window.document.getElementById(`${type}-${id}-toggle`).style.display = 'none';
    }
  }

  render() {
    const {
      childAction,
      childCount,
      collapseHandler,
      element,
      id,
      isOpened,
      modalAction,
      parentAction,
      parentCount,
      showLessLabel,
      showMoreLabel,
      type,
    } = this.props;

    return (
      <div
        role="presentation"
        id={`${type}-${id}`}
        className={`followup-timeline-box followup-timeline-box-collapsible${isOpened() ? '' : ' collapsed'}`}
        onClick={modalAction}
      >
        {!!parentCount && (
        <ArrowButton
          direction="inward"
          label={parentCount.toString()}
          onClick={parentAction}
        />
        )}
        {element}
        {!!childCount && (
        <ArrowButton
          direction="outward"
          label={childCount.toString()}
          onClick={childAction}
        />
        )}
        <div
          id={`${type}-${id}-toggle`}
          className="followup-timeline-box-toggle"
        >
          <RaisedButton
            id={`${type}-${id}-button`}
            label={isOpened() ? showLessLabel : showMoreLabel}
            onClick={
              () => {
                collapseHandler();
                // forceUpdate b/c can't pass in state from FollowUpContainer due to resolveElement
                this.forceUpdate();
                if (isOpened()) {
                  window.document.getElementById(`${type}-${id}`).scrollIntoView();
                }
              }
            }
          />
        </div>
      </div>
    );
  }
}

FollowUpBox.defaultProps = {
  modalAction: null,
};

FollowUpBox.propTypes = {
  childAction: PropTypes.func.isRequired,
  childCount: PropTypes.number.isRequired,
  collapseHandler: PropTypes.func.isRequired,
  element: PropTypes.element.isRequired,
  id: PropTypes.number.isRequired,
  isOpened: PropTypes.func.isRequired,
  modalAction: PropTypes.func,
  parentAction: PropTypes.func.isRequired,
  parentCount: PropTypes.number.isRequired,
  showLessLabel: PropTypes.string.isRequired,
  showMoreLabel: PropTypes.string.isRequired,
  type: PropTypes.oneOf(['contribution', 'snippet', 'document']).isRequired,
};

export default FollowUpBox;

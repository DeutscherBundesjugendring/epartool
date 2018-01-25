import React from 'react';
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
    if (this.props.type === 'contribution') {
      const wellArray = document
        .getElementById(`${this.props.type}-${this.props.id}`)
        .getElementsByClassName('followup-flow')[0];
      // 400px is collapsed followup-timeline-box height, -20px is adjustment for margin
      const newHeight = 400
        - (wellArray.getElementsByClassName('js-followup-box-head')[0].offsetHeight - 20);
      wellArray.getElementsByClassName('js-followup-box-content')[0].style.height
        = this.props.isOpened() ? '' : `${newHeight}px`;
    } else {
      const wellArray = document
        .getElementById(`${this.props.type}-${this.props.id}`)
        .getElementsByClassName('followup-flow')[0];
      const headEl = wellArray.getElementsByClassName('js-followup-box-head')[0];
      // 400px is collapsed followup-timeline-box height, -20px is adjustment for margin
      // 31.01.2018 - jiri@visionapps.cz - 121px is an experimentally found value to prevent two
      // phase resizing of the box when it is collapsing
      const newHeight = 400 - headEl.offsetHeight - 121;
      wellArray.getElementsByClassName('js-followup-box-content')[0].style.height =
        this.props.isOpened()
          ? ''
          : `${newHeight}px`;
    }
  }

  addToggle() {
    /*
    Checks for overflow of box
    Operating on DOM b/c needs to happen after render
    */
    const box = document.getElementById(`${this.props.type}-${this.props.id}`);
    let element = box.getElementsByClassName('followup-flow')[0];

    if (this.props.type === 'contribution') {
      element = element.getElementsByTagName('div')[1];
    }

    const canOverflow = element.scrollHeight > element.clientHeight;
    if (canOverflow) {
      document.getElementById(`${this.props.type}-${this.props.id}-toggle`).style.display = '';
    } else {
      document.getElementById(`${this.props.type}-${this.props.id}-toggle`).style.display = 'none';
    }
  }

  render() {
    return (
      <div
        id={`${this.props.type}-${this.props.id}`}
        className={`followup-timeline-box followup-timeline-box-collapsible${this.props.isOpened() ? '' : ' collapsed'}`}
        onTouchTap={this.props.modalAction}
      >
        {!!this.props.parentCount && <ArrowButton
          direction="inward"
          label={this.props.parentCount.toString()}
          onTouchTap={this.props.parentAction}
        />}
        {this.props.element}
        {!!this.props.childCount && <ArrowButton
          direction="outward"
          label={this.props.childCount.toString()}
          onTouchTap={this.props.childAction}
        />}
        <div
          id={`${this.props.type}-${this.props.id}-toggle`}
          className="followup-timeline-box-toggle"
        >
          <RaisedButton
            id={`${this.props.type}-${this.props.id}-button`}
            label={this.props.isOpened() ? this.props.showLessLabel : this.props.showMoreLabel}
            onTouchTap={
              () => {
                this.props.collapseHandler();
                // forceUpdate b/c can't pass in state from FollowUpContainer due to resolveElement
                this.forceUpdate();
                if (this.props.isOpened()) {
                  document.getElementById(`${this.props.type}-${this.props.id}`).scrollIntoView();
                }
              }
            }
          />
        </div>
      </div>
    );
  }
}

FollowUpBox.propTypes = {
  collapseHandler: React.PropTypes.func.isRequired,
  id: React.PropTypes.number.isRequired,
  type: React.PropTypes.oneOf(['contribution', 'snippet', 'document']).isRequired,
  element: React.PropTypes.element.isRequired,
  isOpened: React.PropTypes.func.isRequired,
  parentCount: React.PropTypes.number.isRequired,
  parentAction: React.PropTypes.func.isRequired,
  childCount: React.PropTypes.number.isRequired,
  childAction: React.PropTypes.func.isRequired,
  modalAction: React.PropTypes.func,
  showLessLabel: React.PropTypes.string.isRequired,
  showMoreLabel: React.PropTypes.string.isRequired,
};

export default FollowUpBox;

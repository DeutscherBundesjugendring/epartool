import React from 'react';
import ArrayButton from '../ArrowButton/ArrowButton';


const FollowUpContributionBox = props => (
  <div className="followup-timeline-box">
    <div className="well well-bordered">
      <p>
        {props.contribution}
      </p>
      <div className="text-accent">
        {props.votingResults}
      </div>
    </div>
    <ArrayButton type="followup-timeline-count" label={props.childCount.toString()} />
  </div>
);

FollowUpContributionBox.propTypes = {
  contribution: React.PropTypes.string.isRequired,
  votingResults: React.PropTypes.string.isRequired,
  childCount: React.PropTypes.number.isRequired,
};

export default FollowUpContributionBox;

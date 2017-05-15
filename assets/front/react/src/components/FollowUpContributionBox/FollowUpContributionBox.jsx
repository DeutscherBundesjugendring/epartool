import React from 'react';


const FollowUpContributionBox = props => (
  <div className="well well-bordered">
    <p>
      {props.contribution}
    </p>
    <div className="text-accent">
      {props.votingResults}
    </div>
  </div>
);

FollowUpContributionBox.propTypes = {
  contribution: React.PropTypes.string.isRequired,
  votingResults: React.PropTypes.string.isRequired,
};

export default FollowUpContributionBox;

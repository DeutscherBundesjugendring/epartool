import React from 'react';
import EmbeddedVideo from '../EmbeddedVideo/EmbeddedVideo';


const FollowUpContributionBox = props => (
  <div className="well well-bordered">
    {!!props.videoService && !!props.videoId &&
      <EmbeddedVideo
        videoService={props.videoService}
        videoId={props.videoId}
      />
    }
    <p>
      {props.contributionThesis}
    </p>
    {!!props.contributionExplanation &&
      <p>
        {props.contributionExplanation}
      </p>
    }
    {props.votable &&
      <div className="text-accent">
        <p>
          {props.votingText}
        </p>
        <p>
          <a href={props.votingLink}>
            {props.votingResults}
          </a>
        </p>
      </div>
    }
  </div>
);

FollowUpContributionBox.propTypes = {
  contributionThesis: React.PropTypes.string.isRequired,
  contributionExplanation: React.PropTypes.string,
  votable: React.PropTypes.bool.isRequired,
  votingText: React.PropTypes.string,
  votingResults: React.PropTypes.string,
  votingLink: React.PropTypes.string,
  videoService: React.PropTypes.string,
  videoId: React.PropTypes.string,
};

export default FollowUpContributionBox;

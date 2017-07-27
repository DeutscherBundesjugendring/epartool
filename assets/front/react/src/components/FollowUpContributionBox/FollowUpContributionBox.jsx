import React from 'react';
import EmbeddedVideo from '../EmbeddedVideo/EmbeddedVideo';


const FollowUpContributionBox = props => (
  <div className="followup-flow">
    <div className="well well-bordered well-deep">
      <h4>
        {!!props.questionNumber &&
          <span className="badge badge-accent offset-right-small">{props.questionNumber}</span>
        }
        {props.question}
      </h4>
    </div>
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
      {!!props.contributionExplanation && <div
        // eslint-disable-next-line react/no-danger
        dangerouslySetInnerHTML={{ __html: props.contributionExplanation }}
      />
      }
      {props.votable &&
        <div className="text-accent offset-top">
          <p>
            {props.votingText}<br />
            <a href={props.votingLink}>
              {props.votingResults}
            </a>
          </p>
        </div>
      }
    </div>
  </div>
);

FollowUpContributionBox.propTypes = {
  contributionThesis: React.PropTypes.string.isRequired,
  contributionExplanation: React.PropTypes.string,
  question: React.PropTypes.string.isRequired,
  questionNumber: React.PropTypes.string,
  votable: React.PropTypes.bool.isRequired,
  votingText: React.PropTypes.string,
  votingResults: React.PropTypes.string,
  votingLink: React.PropTypes.string,
  videoService: React.PropTypes.string,
  videoId: React.PropTypes.string,
};

export default FollowUpContributionBox;

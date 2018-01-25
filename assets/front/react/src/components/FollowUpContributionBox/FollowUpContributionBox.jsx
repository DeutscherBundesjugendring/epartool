import React from 'react';
import EmbeddedVideo from '../EmbeddedVideo/EmbeddedVideo';
import StaticMap from '../StaticMap/StaticMap';


const FollowUpContributionBox = props => (
  <div className="followup-flow">
    <div className="well well-bordered well-deep js-followup-box-head">
      <h4>
        {!!props.questionNumber &&
          <span className="badge badge-accent offset-right-small">{props.questionNumber}</span>
        }
        {props.question}
      </h4>
    </div>
    <div className="well well-bordered followup-well-collapsible js-followup-box-content">
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
      {props.locationEnabled && !!props.latitude && !!props.longitude &&
        <StaticMap
          latitude={props.latitude}
          longitude={props.longitude}
        />
      }
    </div>
  </div>
);

FollowUpContributionBox.propTypes = {
  contributionThesis: React.PropTypes.string.isRequired,
  contributionExplanation: React.PropTypes.string,
  latitude: React.PropTypes.number,
  locationEnabled: React.PropTypes.bool,
  longitude: React.PropTypes.number,
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

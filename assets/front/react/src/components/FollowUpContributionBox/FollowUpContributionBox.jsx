import React from 'react';
import PropTypes from 'prop-types';
import EmbeddedVideo from '../EmbeddedVideo/EmbeddedVideo';
import StaticMap from '../StaticMap/StaticMap';

const FollowUpContributionBox = ({
  contributionExplanation,
  contributionThesis,
  latitude,
  locationEnabled,
  longitude,
  question,
  questionNumber,
  videoId,
  videoService,
  votable,
  votingLink,
  votingResults,
  votingText,
}) => (
  <div className="followup-flow">
    <div className="well well-bordered well-deep js-followup-box-head">
      <h4>
        {!!questionNumber
          && <span className="badge badge-accent offset-right-small">{questionNumber}</span>}
        {question}
      </h4>
    </div>
    <div className="well well-bordered followup-well-collapsible js-followup-box-content">
      {!!videoService
        && !!videoId
        && (<EmbeddedVideo videoService={videoService} videoId={videoId} />)}
      <p>{contributionThesis}</p>
      {!!contributionExplanation && (
      <div
        // eslint-disable-next-line react/no-danger
        dangerouslySetInnerHTML={{ __html: contributionExplanation }}
      />
      )}
      {votable
        && (
        <div className="text-accent offset-top">
          <p>
            {votingText}
            <br />
            <a href={votingLink}>{votingResults}</a>
          </p>
        </div>
        )}
      {locationEnabled
        && !!latitude
        && !!longitude
        && (<StaticMap latitude={latitude} longitude={longitude} />)}
    </div>
  </div>
);

FollowUpContributionBox.defaultProps = {
  contributionExplanation: null,
  latitude: null,
  locationEnabled: false,
  longitude: null,
  questionNumber: null,
  videoId: null,
  videoService: null,
  votingLink: null,
  votingResults: null,
  votingText: null,
};

FollowUpContributionBox.propTypes = {
  contributionExplanation: PropTypes.string,
  contributionThesis: PropTypes.string.isRequired,
  latitude: PropTypes.number,
  locationEnabled: PropTypes.bool,
  longitude: PropTypes.number,
  question: PropTypes.string.isRequired,
  questionNumber: PropTypes.string,
  videoId: PropTypes.string,
  videoService: PropTypes.string,
  votable: PropTypes.bool.isRequired,
  votingLink: PropTypes.string,
  votingResults: PropTypes.string,
  votingText: PropTypes.string,
};

export default FollowUpContributionBox;

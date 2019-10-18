import { shallow } from 'enzyme';
import React from 'react';
import FollowUpContributionBox from '../FollowUpContributionBox';

describe('rendering', () => {
  it('renders correctly with contribution text only', () => {
    const tree = shallow(
      <FollowUpContributionBox
        contributionThesis="Contribution thesis"
        question="Question"
        votable={false}
      />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly with all texts, embedded video, location and voting', () => {
    const tree = shallow(
      <FollowUpContributionBox
        contributionThesis="Contribution thesis"
        contributionExplanation="Contribution explanation"
        locationEnabled
        latitude={1.1}
        longitude={1.2}
        question="Question"
        questionNumber="1"
        videoService="youtube"
        videoId="youtube-video-id"
        votable
        votingText="Voting results"
        votingResults="0 votes"
        votingLink="http://www.example.com/"
      />,
    );

    expect(tree).toMatchSnapshot();
  });
});

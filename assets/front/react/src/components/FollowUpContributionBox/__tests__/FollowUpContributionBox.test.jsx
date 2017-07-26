import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import React from 'react';
import FollowUpContributionBox from '../FollowUpContributionBox';


describe('rendering', () => {
  it('renders correctly with contribution text only', () => {
    const tree = shallow(
      <FollowUpContributionBox
        contributionThesis="Contribution thesis"
        question="Question"
        votable={false}
      />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly with all texts, embedded video and voting', () => {
    const tree = shallow(
      <FollowUpContributionBox
        contributionThesis="Contribution thesis"
        contributionExplanation="Contribution explanation"
        question="Question"
        questionNumber="1"
        videoService="youtube"
        videoId="youtube-video-id"
        votable
        votingText="Voting results"
        votingResults="0 votes"
        votingLink="http://www.example.com/"
      />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import React from 'react';
import FollowUpContributionBox from '../FollowUpContributionBox';


describe('rendering', () => {
  it('renders correctly', () => {
    const tree = shallow(
      <FollowUpContributionBox contribution="Text" votingResults="Text" childCount={1} />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });;
});

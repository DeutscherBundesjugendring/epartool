import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import React from 'react';
import FollowUpTimeline from '../FollowUpTimeline';


describe('rendering', () => {
  it('renders correctly without elements', () => {
    const tree = shallow(
      <FollowUpTimeline
        infoLink="#"
        infoLinkTitle="Link"
        infoText="Text"
      />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly with elements in all columns', () => {
    const tree = shallow(
      <FollowUpTimeline
        infoLink="#"
        infoLinkTitle="Link"
        infoText="Text"
        leftColumn={[<div>Left column element</div>]}
        centerColumn={[<div>Center column element</div>]}
        rightColumn={[<div>Right column element</div>]}
      />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

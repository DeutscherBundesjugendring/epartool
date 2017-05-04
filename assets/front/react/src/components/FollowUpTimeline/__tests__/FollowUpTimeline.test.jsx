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

  it('renders correctly with element in one column and one row', () => {
    const tree = shallow(
      <FollowUpTimeline
        infoLink="#"
        infoLinkTitle="Link"
        infoText="Text"
        columns={[[<div>Left column element</div>]]}
      />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly with elements in more columns and more rows', () => {
    const tree = shallow(
      <FollowUpTimeline
        infoLink="#"
        infoLinkTitle="Link"
        infoText="Text"
        columns={[
          [
            <div>Left column element</div>,
            <div>Center column element</div>,
            <div>Right column element</div>,
          ],
          [
            <div>Left column element</div>,
            <div>Center column element</div>,
          ],
          [
            <div>Left column element</div>,
          ],
        ]}
      />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});
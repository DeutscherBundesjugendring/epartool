import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import sinon from 'sinon';
import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import FollowUpBox from '../FollowUpBox';


injectTapEventPlugin();

describe('rendering', () => {
  it('renders correctly without arrows', () => {
    const tree = shallow(
      <FollowUpBox
        element={<div>Element</div>}
        parentCount={0}
        parentAction={() => {}}
        childCount={0}
        childAction={() => {}}
      />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly with right arrow', () => {
    const tree = shallow(
      <FollowUpBox
        element={<div>Element</div>}
        parentCount={0}
        parentAction={() => {}}
        childCount={1}
        childAction={() => {}}
      />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly with left arrow', () => {
    const tree = shallow(
      <FollowUpBox
        element={<div>Element</div>}
        parentCount={1}
        parentAction={() => {}}
        childCount={0}
        childAction={() => {}}
      />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly with both arrows', () => {
    const tree = shallow(
      <FollowUpBox
        element={<div>Element</div>}
        parentCount={1}
        parentAction={() => {}}
        childCount={1}
        childAction={() => {}}
      />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls modal action', () => {
    const spy = sinon.spy();
    const component = shallow(
      <FollowUpBox
        element={<div>Element</div>}
        parentCount={0}
        parentAction={() => {}}
        childCount={0}
        childAction={() => {}}
        modalAction={spy}
      />
    );

    component.find('div').first().simulate('touchTap', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

import { shallow } from 'enzyme';
import sinon from 'sinon';
import React from 'react';
import FollowUpBox from '../FollowUpBox';

describe('rendering', () => {
  it('renders correctly without arrows', () => {
    const tree = shallow(
      <FollowUpBox
        collapseHandler={() => {}}
        id={1}
        type="contribution"
        element={<div>Element</div>}
        isOpened={() => {}}
        parentCount={0}
        parentAction={() => {}}
        childCount={0}
        childAction={() => {}}
        showLessLabel="Show less"
        showMoreLabel="Show more"
      />,
      { disableLifecycleMethods: true },
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly with right arrow', () => {
    const tree = shallow(
      <FollowUpBox
        collapseHandler={() => {}}
        id={1}
        type="contribution"
        element={<div>Element</div>}
        isOpened={() => {}}
        parentCount={0}
        parentAction={() => {}}
        childCount={1}
        childAction={() => {}}
        showLessLabel="Show less"
        showMoreLabel="Show more"
      />,
      { disableLifecycleMethods: true },
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly with left arrow', () => {
    const tree = shallow(
      <FollowUpBox
        collapseHandler={() => {}}
        id={1}
        type="contribution"
        element={<div>Element</div>}
        isOpened={() => {}}
        parentCount={1}
        parentAction={() => {}}
        childCount={0}
        childAction={() => {}}
        showLessLabel="Show less"
        showMoreLabel="Show more"
      />,
      { disableLifecycleMethods: true },
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly with both arrows', () => {
    const tree = shallow(
      <FollowUpBox
        collapseHandler={() => {}}
        id={1}
        type="contribution"
        element={<div>Element</div>}
        isOpened={() => {}}
        parentCount={1}
        parentAction={() => {}}
        childCount={1}
        childAction={() => {}}
        showLessLabel="Show less"
        showMoreLabel="Show more"
      />,
      { disableLifecycleMethods: true },
    );

    expect(tree).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls modal action', () => {
    const spy = sinon.spy();
    const component = shallow(
      <FollowUpBox
        collapseHandler={() => {}}
        id={1}
        type="contribution"
        element={<div>Element</div>}
        isOpened={() => {}}
        parentCount={0}
        parentAction={() => {}}
        childCount={0}
        childAction={() => {}}
        modalAction={spy}
        showLessLabel="Show less"
        showMoreLabel="Show more"
      />,
      { disableLifecycleMethods: true },
    );

    component.find('div').first().simulate('click', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

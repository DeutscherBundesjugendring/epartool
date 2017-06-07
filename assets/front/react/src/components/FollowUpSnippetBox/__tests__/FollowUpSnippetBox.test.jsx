import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import sinon from 'sinon';
import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import FollowUpSnippetBox from '../FollowUpSnippetBox';


injectTapEventPlugin();

const types = ['general', 'supporting', 'action', 'rejected', 'end'];
const getElement = type => (
  <FollowUpSnippetBox
    type={type}
    snippetExplanation="Snippet"
    likeAction={() => {}}
    likeCount={0}
    dislikeAction={() => {}}
    dislikeCount={0}
    followPathAction={() => {}}
    followPathLabel="Follow path"
  />
);

describe('rendering', () => {
  types.forEach((type) => {
    it('renders correctly', () => {
      const tree = shallow(React.cloneElement(getElement(type)));
      expect(shallowToJson(tree)).toMatchSnapshot();
    });
  });
});

describe('functionality', () => {
  it('calls follow path action', () => {
    const spy = sinon.spy();
    const component = shallow(
      React.cloneElement(getElement(types[0]), { followPathAction: spy })
    );

    component.find('RaisedButton').first().simulate('touchTap', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import sinon from 'sinon';
import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import FollowUpSnippetBox from '../FollowUpSnippetBox';


injectTapEventPlugin();

const element = <FollowUpSnippetBox
  snippet="Snippet"
  likeAction={() => {}}
  likeCount={0}
  dislikeAction={() => {}}
  dislikeCount={0}
  continueAction={() => {}}
  continueLabel="Continue"
/>;

describe('rendering', () => {
  it('renders correctly', () => {
    const tree = shallow(
      React.cloneElement(element, { snippetImageSrc: 'image.jpg' })
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly without optional props', () => {
    const tree = shallow(
      React.cloneElement(element)
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls like action', () => {
    const spy = sinon.spy();
    const component = shallow(
      React.cloneElement(element, { likeAction: spy })
    );

    component.find('ThumbButton').first().simulate('touchTap', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });

  it('calls dislike action', () => {
    const spy = sinon.spy();
    const component = shallow(
      React.cloneElement(element, { dislikeAction: spy })
    );

    component.find('ThumbButton').at(1).simulate('touchTap', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });

  it('calls continue action', () => {
    const spy = sinon.spy();
    const component = shallow(
      React.cloneElement(element, { continueAction: spy })
    );

    component.find('RaisedButton').first().simulate('touchTap', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

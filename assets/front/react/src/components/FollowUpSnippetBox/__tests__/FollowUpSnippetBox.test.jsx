import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import sinon from 'sinon';
import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import FollowUpSnippetBox from '../FollowUpSnippetBox';


injectTapEventPlugin();

describe('rendering', () => {
  it('renders correctly', () => {
    const tree = shallow(
      <FollowUpSnippetBox
        snippet="Snippet"
        snippetImageSrc="image.jpg"
        modalAction={() => {}}
        likeAction={() => {}}
        likeCount={0}
        dislikeAction={() => {}}
        dislikeCount={0}
        continueAction={() => {}}
        continueLabel="Continue"
      />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly without optional props', () => {
    const tree = shallow(
      <FollowUpSnippetBox
        snippet="Snippet"
        modalAction={() => {}}
        likeAction={() => {}}
        likeCount={0}
        dislikeAction={() => {}}
        dislikeCount={0}
        continueAction={() => {}}
        continueLabel="Continue"
      />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls modal action', () => {
    const spy = sinon.spy();
    const component = shallow(
      <FollowUpSnippetBox
        snippet="Snippet"
        snippetImageSrc="example.jpg"
        modalAction={spy}
        likeAction={() => {}}
        likeCount={0}
        dislikeAction={() => {}}
        dislikeCount={0}
        continueAction={() => {}}
        continueLabel="Continue"
      />
    );

    component.find('div').first().simulate('touchTap', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });

  it('calls like action', () => {
    const spy = sinon.spy();
    const component = shallow(
      <FollowUpSnippetBox
        snippet="Snippet"
        snippetImageSrc="example.jpg"
        modalAction={() => {}}
        likeAction={spy}
        likeCount={0}
        dislikeAction={() => {}}
        dislikeCount={0}
        continueAction={() => {}}
        continueLabel="Continue"
      />
    );

    component.find('ThumbButton').first().simulate('touchTap', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });

  it('calls dislike action', () => {
    const spy = sinon.spy();
    const component = shallow(
      <FollowUpSnippetBox
        snippet="Snippet"
        snippetImageSrc="example.jpg"
        modalAction={() => {}}
        likeAction={() => {}}
        likeCount={0}
        dislikeAction={spy}
        dislikeCount={0}
        continueAction={() => {}}
        continueLabel="Continue"
      />
    );

    component.find('ThumbButton').at(1).simulate('touchTap', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });

  it('calls continue action', () => {
    const spy = sinon.spy();
    const component = shallow(
      <FollowUpSnippetBox
        snippet="Snippet"
        snippetImageSrc="example.jpg"
        modalAction={() => {}}
        likeAction={() => {}}
        likeCount={0}
        dislikeAction={() => {}}
        dislikeCount={0}
        continueAction={spy}
        continueLabel="Continue"
      />
    );

    component.find('RaisedButton').first().simulate('touchTap', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

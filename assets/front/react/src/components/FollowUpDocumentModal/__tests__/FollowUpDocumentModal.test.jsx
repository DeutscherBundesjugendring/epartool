import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import sinon from 'sinon';
import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import FollowUpDocumentModal from '../FollowUpDocumentModal';


injectTapEventPlugin();

const element = (
  <FollowUpDocumentModal
    title="Document title"
    author="Author of document"
    date={new Date()}
    dateMonthYearOnly={false}
    previewImageLink="http://www.example.com/image.jpg"
    downloadAction={() => {}}
    downloadLabel="Download document"
    closeAction={() => {}}
  />
);

const snippetElement = {
  snippetExplanation: 'Snippet',
  likeAction: () => {},
  likeCount: 0,
  dislikeAction: () => {},
  dislikeCount: 0,
  followPathAction: () => {},
  followPathLabel: 'Follow path',
};


describe('rendering', () => {
  it('renders correctly with long date without snippets', () => {
    const tree = shallow(
      React.cloneElement(element)
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly with short date without snippets', () => {
    const tree = shallow(
      React.cloneElement(element, { dateMonthYearOnly: true })
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly with one snippet', () => {
    const tree = shallow(
      React.cloneElement(element, { snippets: [
        Object.assign({}, snippetElement),
      ] })
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls close action', () => {
    const spy = sinon.spy();
    const component = shallow(
      React.cloneElement(element, { closeAction: spy })
    );

    component.find('button').first().simulate('touchTap', { stopPropagation: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });

  it('calls download action', () => {
    const spy = sinon.spy();
    const component = shallow(
      React.cloneElement(element, { downloadAction: spy })
    );

    component.find('button').last().simulate('touchTap', { stopPropagation: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });

  it('calls snippet like action', () => {
    const spy = sinon.spy();
    const snippetElementCopy = Object.assign({}, snippetElement);
    snippetElementCopy.likeAction = spy;

    const component = shallow(
      React.cloneElement(element, { snippets: [snippetElementCopy] })
    );

    component.find('ThumbButton').first().simulate('touchTap');
    expect(spy.calledOnce).toEqual(true);
  });

  it('calls snippet dislike action', () => {
    const spy = sinon.spy();
    const snippetElementCopy = Object.assign({}, snippetElement);
    snippetElementCopy.dislikeAction = spy;

    const component = shallow(
      React.cloneElement(element, { snippets: [snippetElementCopy] })
    );

    component.find('ThumbButton').last().simulate('touchTap');
    expect(spy.calledOnce).toEqual(true);
  });

  it('calls snippet follow path action', () => {
    const spy = sinon.spy();
    const snippetElementCopy = Object.assign({}, snippetElement);
    snippetElementCopy.followPathAction = spy;

    const component = shallow(
      React.cloneElement(element, { snippets: [snippetElementCopy] })
    );

    component.find('RaisedButton').simulate('touchTap');
    expect(spy.calledOnce).toEqual(true);
  });
});

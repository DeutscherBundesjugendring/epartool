import { shallow } from 'enzyme';
import sinon from 'sinon';
import React from 'react';
import FollowUpDocumentModal from '../FollowUpDocumentModal';

const types = ['general', 'supporting', 'action', 'rejected', 'end'];
const getElement = (type) => (
  <FollowUpDocumentModal
    type={type}
    title="Document title"
    author="Author of document"
    date={new Date('2017-04-02')}
    dateMonthYearOnly={false}
    previewImageLink="http://www.example.com/image.jpg"
    downloadAction={() => {}}
    downloadLabel="Download document"
    closeAction={() => {}}
    typeActionLabel="typeAction"
    typeEndLabel="typeEnd"
    typeRejectedLabel="typeRejected"
    typeSupportingLabel="typeSupporting"
  />
);
const snippetElement = {
  dislikeAction: () => {},
  dislikeCount: 0,
  dislikeLabel: 'dislike',
  followPathAction: () => {},
  followPathLabel: 'Follow path',
  likeAction: () => {},
  likeCount: 0,
  likeLabel: 'like',
  showFollowPathButton: true,
  snippetExplanation: 'Snippet',
  votingLimitError: 'voting-error',
};

describe('rendering', () => {
  types.forEach((type) => {
    it('renders correctly with long date without snippets', () => {
      const tree = shallow(
        React.cloneElement(getElement(type)),
      );

      expect(tree).toMatchSnapshot();
    });
  });

  it('renders correctly with short date without snippets', () => {
    const tree = shallow(
      React.cloneElement(getElement(types[0]), { dateMonthYearOnly: true }),
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly with one snippet', () => {
    const tree = shallow(
      React.cloneElement(getElement(types[0]), {
        snippets: [
          { ...snippetElement },
        ],
      }),
    );

    expect(tree).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls close action', () => {
    const spy = sinon.spy();
    const component = shallow(
      React.cloneElement(getElement(types[0]), { closeAction: spy }),
    );

    component.find('button').first().simulate('click', { stopPropagation: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });

  it('calls download action', () => {
    const spy = sinon.spy();
    const component = shallow(
      React.cloneElement(getElement(types[0]), { downloadAction: spy }),
    );

    component.find('button').last().simulate('click', { stopPropagation: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });

  it('calls snippet follow path action', () => {
    const spy = sinon.spy();
    const snippetElementCopy = { ...snippetElement };
    snippetElementCopy.followPathAction = spy;

    const component = shallow(
      React.cloneElement(getElement(types[0]), { snippets: [snippetElementCopy] }),
    );

    component.find('RaisedButton').simulate('click');
    expect(spy.calledOnce).toEqual(true);
  });
});

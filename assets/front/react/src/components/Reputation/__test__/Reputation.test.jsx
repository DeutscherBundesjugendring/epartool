import { shallow } from 'enzyme';
import sinon from 'sinon';
import React from 'react';
import Reputation from '../Reputation';

jest.spyOn(window, 'alert').mockImplementation(() => {
  throw new Error('Alert should not be called.');
});

const element = (
  <Reputation
    likeAction={() => {}}
    likeCount={0}
    likeLabel="like"
    dislikeAction={() => {}}
    dislikeCount={0}
    dislikeLabel="dislike"
    votingLimitError="voting-error"
  />
);

describe('rendering', () => {
  it('renders correctly', () => {
    const tree = shallow(
      React.cloneElement(element),
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly disabled voting', () => {
    const tree = shallow(
      React.cloneElement(element),
    );

    expect(tree).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls like action', () => {
    const spy = sinon.spy();
    const component = shallow(
      React.cloneElement(element, { likeAction: spy }),
    );

    component.find('ThumbButton').first().simulate('click', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });

  it('calls dislike action', () => {
    const spy = sinon.spy();
    const component = shallow(
      React.cloneElement(element, { dislikeAction: spy }),
    );

    component.find('ThumbButton').at(1).simulate('click', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

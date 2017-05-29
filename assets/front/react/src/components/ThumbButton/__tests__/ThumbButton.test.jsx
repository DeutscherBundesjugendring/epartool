import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import sinon from 'sinon';
import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import ThumbButton from '../ThumbButton';


injectTapEventPlugin();

describe('rendering', () => {
  it('renders correctly like', () => {
    const tree = shallow(
      <ThumbButton type="like" onTouchTap={() => {}} />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly dislike', () => {
    const tree = shallow(
      <ThumbButton type="dislike" onTouchTap={() => {}} />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly disabled like', () => {
    const tree = shallow(
      <ThumbButton type="like" onTouchTap={() => {}} disabled />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly disabled dislike', () => {
    const tree = shallow(
      <ThumbButton type="dislike" onTouchTap={() => {}} disabled />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls onTouchTap', () => {
    const spy = sinon.spy();
    const component = shallow(
      <ThumbButton type="like" onTouchTap={spy} />
    );

    component.simulate('touchTap', { stopPropagation: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

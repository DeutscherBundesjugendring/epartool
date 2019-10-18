import { shallow } from 'enzyme';
import sinon from 'sinon';
import React from 'react';
import ThumbButton from '../ThumbButton';

describe('rendering', () => {
  it('renders correctly like', () => {
    const tree = shallow(
      <ThumbButton type="like" onClick={() => {}} label="label" />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly dislike', () => {
    const tree = shallow(
      <ThumbButton type="dislike" onClick={() => {}} label="label" />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly disabled like', () => {
    const tree = shallow(
      <ThumbButton type="like" onClick={() => {}} disabled label="label" />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly disabled dislike', () => {
    const tree = shallow(
      <ThumbButton type="dislike" onClick={() => {}} disabled label="label" />,
    );

    expect(tree).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls onClick', () => {
    const spy = sinon.spy();
    const component = shallow(
      <ThumbButton type="like" onClick={spy} label="label" />,
    );

    component.simulate('click', { stopPropagation: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

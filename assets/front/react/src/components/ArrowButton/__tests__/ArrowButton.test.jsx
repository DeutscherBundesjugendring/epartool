import { shallow } from 'enzyme';
import sinon from 'sinon';
import React from 'react';
import ArrowButton from '../ArrowButton';

describe('rendering', () => {
  it('renders correctly right', () => {
    const tree = shallow(
      <ArrowButton label="1" direction="outward" onClick={() => {}} />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly left', () => {
    const tree = shallow(
      <ArrowButton label="1" direction="inward" onClick={() => {}} />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly right disabled', () => {
    const tree = shallow(
      <ArrowButton label="1" direction="outward" onClick={() => {}} disabled />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly left disabled', () => {
    const tree = shallow(
      <ArrowButton label="1" direction="inward" onClick={() => {}} disabled />,
    );

    expect(tree).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls onClick', () => {
    const spy = sinon.spy();
    const component = shallow(
      <ArrowButton label="1" direction="outward" onClick={spy} />,
    );

    component.simulate('click', { stopPropagation: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

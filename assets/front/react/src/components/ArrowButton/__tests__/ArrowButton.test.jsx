import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import sinon from 'sinon';
import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import ArrowButton from '../ArrowButton';


injectTapEventPlugin();

describe('rendering', () => {
  it('renders correctly right', () => {
    const tree = shallow(
      <ArrowButton label="1" direction="outward" onTouchTap={() => {}} />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly left', () => {
    const tree = shallow(
      <ArrowButton label="1" direction="inward" onTouchTap={() => {}} />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly right disabled', () => {
    const tree = shallow(
      <ArrowButton label="1" direction="outward" onTouchTap={() => {}} disabled />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly left disabled', () => {
    const tree = shallow(
      <ArrowButton label="1" direction="inward" onTouchTap={() => {}} disabled />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls onTouchTap', () => {
    const spy = sinon.spy();
    const component = shallow(
      <ArrowButton label="1" direction="outward" onTouchTap={spy} />
    );

    component.simulate('touchTap', { stopPropagation: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

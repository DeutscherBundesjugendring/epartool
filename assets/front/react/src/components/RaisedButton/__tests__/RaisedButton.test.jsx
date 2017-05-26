import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import sinon from 'sinon';
import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import RaisedButton from '../RaisedButton';


injectTapEventPlugin();

describe('rendering', () => {
  it('renders correctly', () => {
    const tree = shallow(
      <RaisedButton label="text" onTouchTap={() => {}} />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly disabled button', () => {
    const tree = shallow(
      <RaisedButton label="text" onTouchTap={() => {}} disabled />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls onTouchTap', () => {
    const spy = sinon.spy();
    const component = shallow(
      <RaisedButton label="text" onTouchTap={spy} />
    );

    component.simulate('touchTap', { stopPropagation: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

import { shallow } from 'enzyme';
import sinon from 'sinon';
import React from 'react';
import RaisedButton from '../RaisedButton';

describe('rendering', () => {
  it('renders correctly', () => {
    const tree = shallow(
      <RaisedButton label="text" onClick={() => {}} />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly disabled button', () => {
    const tree = shallow(
      <RaisedButton label="text" onClick={() => {}} disabled />,
    );

    expect(tree).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls onClick', () => {
    const spy = sinon.spy();
    const component = shallow(
      <RaisedButton label="text" onClick={spy} />,
    );

    component.simulate('click', { stopPropagation: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

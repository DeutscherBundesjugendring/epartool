import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import React from 'react';
import ArrowButton from '../ArrowButton';


describe('rendering', () => {
  it('renders correctly right', () => {
    const tree = shallow(
      <ArrowButton type="followup-timeline-count" label="1" direction="right" />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly left', () => {
    const tree = shallow(
      <ArrowButton type="followup-timeline-count" label="1" direction="left" />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

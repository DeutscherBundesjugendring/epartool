import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import sinon from 'sinon';
import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import Reputation from '../Reputation';


injectTapEventPlugin();

const element = (
    <Reputation
        likeAction={() => {}}
        likeCount={0}
        dislikeAction={() => {}}
        dislikeCount={0}
    />
);

describe('rendering', () => {
  it('renders correctly', () => {
    const tree = shallow(
      React.cloneElement(element)
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly disabled voting', () => {
    const tree = shallow(
        React.cloneElement(element)
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

describe('functionality', () => {
    it('calls like action', () => {
        const spy = sinon.spy();
        const component = shallow(
            React.cloneElement(element, { likeAction: spy })
        );

        component.find('ThumbButton').first().simulate('touchTap', { preventDefault: () => {} });
        expect(spy.calledOnce).toEqual(true);
    });

    it('calls dislike action', () => {
        const spy = sinon.spy();
        const component = shallow(
            React.cloneElement(element, { dislikeAction: spy })
        );

        component.find('ThumbButton').at(1).simulate('touchTap', { preventDefault: () => {} });
        expect(spy.calledOnce).toEqual(true);
    });
});

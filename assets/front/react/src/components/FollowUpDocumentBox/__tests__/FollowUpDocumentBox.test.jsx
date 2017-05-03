import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import sinon from 'sinon';
import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import FollowUpDocumentBox from '../FollowUpDocumentBox';


injectTapEventPlugin();

describe('rendering', () => {
  it('renders correctly', () => {
    const tree = shallow(
      <FollowUpDocumentBox
        document="Document"
        modalAction={() => {}}
      />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls modal action', () => {
    const spy = sinon.spy();
    const component = shallow(
      <FollowUpDocumentBox
        document="Document"
        modalAction={spy}
      />
    );

    component.find('div').first().simulate('touchTap', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

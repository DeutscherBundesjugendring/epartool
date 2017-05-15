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
      <FollowUpDocumentBox document="Document" />
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import sinon from 'sinon';
import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import FollowUpDocumentBox from '../FollowUpDocumentBox';


injectTapEventPlugin();

const element = (
  <FollowUpDocumentBox
    type="general"
    title="Document title"
    author="Author of document"
    description="Description of document"
    date={new Date('2017-01-01')}
    dateMonthYearOnly={false}
    previewImageLink="http://www.example.com/image.jpg"
    downloadAction={() => {}}
    downloadLabel="Download document"
  />
);


describe('rendering', () => {
  it('renders correctly with long date', () => {
    const tree = shallow(
      React.cloneElement(element)
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });

  it('renders correctly with short date', () => {
    const tree = shallow(
      React.cloneElement(element, { dateMonthYearOnly: true })
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

describe('functionality', () => {
  it('calls download action', () => {
    const spy = sinon.spy();
    const component = shallow(
      React.cloneElement(element, { downloadAction: spy })
    );

    component.find('RaisedButton').first().simulate('touchTap', { preventDefault: () => {} });
    expect(spy.calledOnce).toEqual(true);
  });
});

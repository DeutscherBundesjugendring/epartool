import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';
import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import FollowUpDocumentBox from '../FollowUpDocumentBox';


injectTapEventPlugin();

const types = ['general', 'supporting', 'action', 'rejected', 'end'];
const getElement = type => (
  <FollowUpDocumentBox
    type={type}
    title="Document title"
    author="Author of document"
    description="Description of document"
    date={new Date('2017-01-01')}
    dateMonthYearOnly={false}
    previewImageLink="http://www.example.com/image.jpg"
    typeActionLabel="typeAction"
    typeEndLabel="typeEnd"
    typeRejectedLabel="typeRejected"
    typeSupportingLabel="typeSupporting"
  />
);


describe('rendering', () => {
  types.forEach((type) => {
    it('renders correctly with long date', () => {
      const tree = shallow(
        React.cloneElement(getElement(type))
      );

      expect(shallowToJson(tree)).toMatchSnapshot();
    });
  });

  it('renders correctly with short date', () => {
    const tree = shallow(
      React.cloneElement(getElement(types[0]), { dateMonthYearOnly: true })
    );

    expect(shallowToJson(tree)).toMatchSnapshot();
  });
});

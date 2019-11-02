import { shallow } from 'enzyme';
import React from 'react';
import FollowUpDocumentBox from '../FollowUpDocumentBox';

const types = ['general', 'supporting', 'action', 'rejected', 'end'];
const getElement = (type) => (
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
        React.cloneElement(getElement(type)),
      );

      expect(tree).toMatchSnapshot();
    });
  });

  it('renders correctly with short date', () => {
    const tree = shallow(
      React.cloneElement(getElement(types[0]), { dateMonthYearOnly: true }),
    );

    expect(tree).toMatchSnapshot();
  });
});

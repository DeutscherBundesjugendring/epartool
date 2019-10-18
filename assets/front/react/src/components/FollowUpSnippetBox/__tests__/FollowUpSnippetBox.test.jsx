import { shallow } from 'enzyme';
import React from 'react';
import FollowUpSnippetBox from '../FollowUpSnippetBox';

const types = ['general', 'supporting', 'action', 'rejected', 'end'];
const getElement = (type) => (
  <FollowUpSnippetBox
    type={type}
    snippetExplanation="Snippet"
    likeAction={() => {}}
    likeCount={0}
    likeLabel="like"
    dislikeAction={() => {}}
    dislikeCount={0}
    dislikeLabel="dislike"
    typeActionLabel="typeAction"
    typeEndLabel="typeEnd"
    typeRejectedLabel="typeRejected"
    typeSupportingLabel="typeSupporting"
    votingLimitError="voting-error"
    document={{
      previewImageLink: 'abc',
      title: 'title',
    }}
  />
);

describe('rendering', () => {
  types.forEach((type) => {
    it('renders correctly', () => {
      const tree = shallow(React.cloneElement(getElement(type)));
      expect(tree).toMatchSnapshot();
    });
  });
});

import React from 'react';
import FollowUpBox from '../components/FollowUpBox/FollowUpBox';
import FollowUpContributionBox from '../components/FollowUpContributionBox/FollowUpContributionBox';
import FollowUpDocumentBox from '../components/FollowUpDocumentBox/FollowUpDocumentBox';
import FollowUpSnippetBox from '../components/FollowUpSnippetBox/FollowUpSnippetBox';


const resolveElement = (data, parentAction, childAction) => {
  let element = null;

  if (data.type === 'contribution') {
    element = (
      <FollowUpContributionBox
        contribution={data.expl}
        votingResults={data.votes}
      />
    );
  }

  if (data.type === 'snippet') {
    element = (
      <FollowUpSnippetBox
        snippet={data.expl}
        likeAction={() => {}}
        likeCount={data.lkyea}
        dislikeAction={() => {}}
        dislikeCount={data.lknay}
        continueAction={() => {}}
        continueLabel=""
      />
    );
  }

  if (data.type === 'document') {
    element = (
      <FollowUpDocumentBox
        document=""
      />
    );
  }

  if (element) {
    return (
      <FollowUpBox
        element={element}
        parentCount={data.parent_count}
        parentAction={parentAction}
        childCount={data.children_count}
        childAction={childAction}
      />
    );
  }

  return element;
};

export default resolveElement;

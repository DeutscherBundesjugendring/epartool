import React from 'react';
import FollowUpBox from '../components/FollowUpBox/FollowUpBox';
import FollowUpContributionBox from '../components/FollowUpContributionBox/FollowUpContributionBox';
import FollowUpDocumentBox from '../components/FollowUpDocumentBox/FollowUpDocumentBox';
import FollowUpSnippetBox from '../components/FollowUpSnippetBox/FollowUpSnippetBox';


const resolveElement = (response, parentAction, childAction) => {
  const { data } = response;
  let element = null;

  if (response.type === 'contribution') {
    element = (
      <FollowUpContributionBox
        contributionThesis={data.thes}
        contributionExplanation={data.expl}
        videoService={data.video_service}
        videoId={data.video_id}
        votable={data.is_votable}
        votingText="Ergebnis der Abstimmung:"
        votingResults={`${data.votes}. Rang`}
        votingLink={() => {
          window.location = `/input/show/kid/${response.kid}/qid/${data.qi}`;
        }}
      />
    );
  }

  if (response.type === 'snippet') {
    element = (
      // TODO: video_service and video_id is not returned by API, embed is returned instead
      // Bedrich Schindler <bedrich@visionapps.cz>, 24. 5. 2017 12:33
      <FollowUpSnippetBox
        snippetExplanation={data.expl}
        likeAction={() => {}}
        likeCount={data.lkyea}
        dislikeAction={() => {}}
        dislikeCount={data.lknay}
        followPathAction={() => {
          window.location = `/followup/show-by-snippet/kid/${response.kid}/fid/${data.fid}`;
        }}
        followPathLabel="Diesem Pfad folgen"
        videoService={data.video_service}
        videoId={data.video_id}
      />
    );
  }

  if (response.type === 'document') {
    element = (
      <FollowUpDocumentBox
        title={data.titl}
        author={data.who}
        description={data.ref_view}
        previewImageLink={data.gfx_who}
        downloadAction={() => {
          window.location = data.ref_doc;
        }}
        downloadLabel="Dokument herunterladen"
      />
    );
  }

  if (element) {
    return (
      <FollowUpBox
        element={element}
        parentCount={response.parent_count}
        parentAction={parentAction}
        childCount={response.children_count}
        childAction={childAction}
        modalAction={() => {}}
      />
    );
  }

  return element;
};

export default resolveElement;

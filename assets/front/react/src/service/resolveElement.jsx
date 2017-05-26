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
        votable={!!data.is_votable}
        votingText="Ergebnis der Abstimmung:"
        votingResults={`${data.votes}. Rang`}
        votingLink={`/input/show/kid/${response.kid}/qid/${data.qi}`}
      />
    );
  }

  if (response.type === 'snippet') {
    element = (
      <FollowUpSnippetBox
        snippetExplanation={data.expl}
        likeAction={() => {}}
        likeCount={parseInt(data.lkyea, 10)}
        dislikeAction={() => {}}
        dislikeCount={parseInt(data.lknay, 10)}
        followPathAction={() => {
          window.location = `/followup/show-by-snippet/kid/${response.kid}/fid/${data.fid}`;
        }}
        followPathLabel="Diesem Pfad folgen"
      />
    );
  }

  if (response.type === 'document') {
    element = (
      <FollowUpDocumentBox
        title={data.titl}
        author={data.who}
        description={data.ref_view}
        date={new Date(data.when)}
        dateMonthYearOnly={!!data.is_only_month_year_showed}
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
        id={parseInt(response.id, 10)}
        type={response.type}
        element={element}
        parentCount={parseInt(response.parents_count, 10)}
        parentAction={parentAction}
        childCount={parseInt(response.children_count, 10)}
        childAction={childAction}
        modalAction={() => {}}
      />
    );
  }

  return element;
};

export default resolveElement;

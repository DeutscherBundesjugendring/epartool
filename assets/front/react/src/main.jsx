import React from 'react';
import ReactDOM from 'react-dom';
import injectTapEventPlugin from 'react-tap-event-plugin';
import resolvePath from './service/resolvePath';
import resolveElement from './service/resolveElement';
import { followUpPath } from './config/config';
import { fetchFollowUpElement } from './actions';
import FollowUpTimeline from './components/FollowUpTimeline/FollowUpTimeline';
import FollowUpSnippetBox from './components/FollowUpSnippetBox/FollowUpSnippetBox';



injectTapEventPlugin();

const renderFollowUpTimeline = (path, element) => {
  ReactDOM.render(
    <FollowUpTimeline
      infoLink={`${followUpPath}/index/kid/${path.consultationId}`}
      infoLinkTitle="Zpět na přehled příspěvků a reakcí."
      infoText="Zde sleduj reakce na tvé příspěvky. Pro další kroky klikni na šipky."
      columns={[[element]]}
    />,
    document.getElementById('followup-timeline')
  );
};

const initializeFollowUpTimeline = () => {
  let path = resolvePath(location.pathname, followUpPath);

  // DEVELOPMENT ONLY!
  path = {
    consultationId: 1,
    followUpId: 1,
  };

  if (path) {
    fetchFollowUpElement(path.consultationId, path.followUpId)
      .then((response) => {
        if (response.type === 'snippet') {
          renderFollowUpTimeline(
            path,
            resolveElement(response, () => console.log('Parent'), () => console.log('Child'))
          );
        }
      })
      .catch(error => console.error(error));
  }
};

// initializeFollowUpTimeline();

renderFollowUpTimeline(
  <FollowUpSnippetBox
    snippet="Snippet content"
    likeAction={() => {}}
    likeCount={0}
    dislikeAction={() => {}}
    dislikeCount={0}
    continueAction={() => {}}
    continueLabel="Continue"
  />
);
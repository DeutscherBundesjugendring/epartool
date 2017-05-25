import React from 'react';
import ReactDOM from 'react-dom';
import injectTapEventPlugin from 'react-tap-event-plugin';
import resolvePath from './service/resolvePath';
import resolveElement from './service/resolveElement';
import FollowUp from './components/FollowUp/FollowUp';


injectTapEventPlugin();

const resolvedPath = resolvePath(location.pathname);

ReactDOM.render(
  <FollowUp followUpType={resolvedPath.followUpType} followUpId={resolvedPath.followUpId} />,
  document.getElementById('followup-timeline')
);






























fetchFollowUpElement(path.type, path.followUpId)
  .then((response) => {
    if (response.type === 'snippet') {
      renderFollowUpTimeline(
        path,
        resolveElement(response, () => console.log('Parent'), () => console.log('Child'))
      );
    }
  })
  .catch(error => console.error(error));
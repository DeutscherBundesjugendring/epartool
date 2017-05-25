import React from 'react';
import ReactDOM from 'react-dom';
import injectTapEventPlugin from 'react-tap-event-plugin';
import resolvePath from './service/resolvePath';
import FollowUpContainer from './components/FollowUpContainer/FollowUpContainer';


injectTapEventPlugin();

const resolvedPath = resolvePath(location.pathname);

ReactDOM.render(
  <FollowUpContainer
    consultationId={resolvedPath.consultationId}
    followUpType={resolvedPath.followUpType}
    followUpId={resolvedPath.followUpId}
  />,
  document.getElementById('followup-timeline')
);

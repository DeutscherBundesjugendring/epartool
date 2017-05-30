import React from 'react';
import ReactDOM from 'react-dom';
import injectTapEventPlugin from 'react-tap-event-plugin';
import resolvePath from './service/resolvePath';
import FollowUpContainer from './modules/FollowUpContainer/FollowUpContainer';
import ReactionsAndImpactContainer from './modules/ReactionsAndImpactContainer/ReactionsAndImpactContainer';


injectTapEventPlugin();

const path = resolvePath(location.pathname);

if (path) {
  if (path.type === 'followup-timeline') {
    ReactDOM.render(
      <FollowUpContainer
        consultationId={path.consultationId}
        followUpType={path.followUpType}
        followUpId={path.followUpId}
      />,
      document.getElementById('followup-timeline')
    );
  }

  if (path.type === 'reactions-and-impact') {
    const reactionAndImpact = document.getElementById('reactions-and-impact');
    const viewDocumentModal = document.getElementsByClassName('js-reactions-and-impact-view');

    Array.from(viewDocumentModal).forEach((btn) => {
      btn.addEventListener('click', (event) => {
        const followUpId = parseInt(event.target.dataset.ffid, 10);

        // eslint-disable-next-line react/no-find-dom-node
        const reactionAndImpactReact = ReactDOM.findDOMNode(reactionAndImpact);
        if (reactionAndImpactReact) {
          ReactDOM.unmountComponentAtNode(reactionAndImpactReact);
        }

        window.addModalOpenToBody();
        ReactDOM.render(
          <ReactionsAndImpactContainer followUpId={followUpId} />,
          reactionAndImpact
        );
      });
    });
  }
}


import React from 'react';
import Reputation from '../Reputation/Reputation';
import RaisedButton from '../RaisedButton/RaisedButton';


const FollowUpSnippetBox = props => (
  <div className="well well-bordered followup-flow followup-well followup-well-link">
    <div dangerouslySetInnerHTML={{ __html: props.snippetExplanation }} />
    <div className="offset-bottom-small">
      <Reputation
        likeCount={props.likeCount}
        dislikeCount={props.dislikeCount}
        likeAction={props.likeAction}
        dislikeAction={props.dislikeAction}
      />
    </div>
    <RaisedButton label={props.followPathLabel} onTouchTap={props.followPathAction} />
  </div>
);

FollowUpSnippetBox.propTypes = {
  snippetExplanation: React.PropTypes.string.isRequired,
  likeAction: React.PropTypes.func.isRequired,
  likeCount: React.PropTypes.number.isRequired,
  dislikeAction: React.PropTypes.func.isRequired,
  dislikeCount: React.PropTypes.number.isRequired,
  followPathAction: React.PropTypes.func.isRequired,
  followPathLabel: React.PropTypes.string.isRequired,
};

export default FollowUpSnippetBox;

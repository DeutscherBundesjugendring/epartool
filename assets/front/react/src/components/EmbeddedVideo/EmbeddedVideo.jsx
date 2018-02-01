import React from 'react';

/* global embedVideoUrl */

const EmbeddedVideo = (props) => {
  let src = null;

  if (embedVideoUrl[props.videoService] !== undefined) {
    src = embedVideoUrl[props.videoService].replace('%s', props.videoId);
  }

  if (src) {
    return (
      <iframe
        width={props.width}
        height={props.height}
        src={src}
        className="followup-video"
      />
    );
  }

  return null;
};

EmbeddedVideo.defaultProps = {
  width: 240,
  height: 120,
};

EmbeddedVideo.propTypes = {
  videoService: React.PropTypes.oneOf(['youtube', 'vimeo', 'facebook']).isRequired,
  videoId: React.PropTypes.string.isRequired,
  width: React.PropTypes.number,
  height: React.PropTypes.number,
};

export default EmbeddedVideo;

import React from 'react';
import PropTypes from 'prop-types';

/* global embedVideoUrl */

const EmbeddedVideo = (props) => {
  const {
    height,
    videoService,
    videoId,
    width,
  } = props;
  let src = null;

  if (embedVideoUrl[videoService] !== undefined) {
    src = embedVideoUrl[videoService].replace('%s', videoId);
  }

  if (src) {
    return (
      <iframe
        title={`Video ${videoId}`}
        width={width}
        height={height}
        src={src}
        className="followup-video"
      />
    );
  }

  return null;
};

EmbeddedVideo.defaultProps = {
  height: 120,
  width: 240,
};

EmbeddedVideo.propTypes = {
  height: PropTypes.number,
  videoId: PropTypes.string.isRequired,
  videoService: PropTypes.oneOf(['youtube', 'vimeo', 'facebook']).isRequired,
  width: PropTypes.number,
};

export default EmbeddedVideo;

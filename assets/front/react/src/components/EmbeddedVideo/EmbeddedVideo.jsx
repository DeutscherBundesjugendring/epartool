import React from 'react';


const EmbeddedVideo = (props) => {
  if (props.videoService === 'youtube') {
    return (
      <iframe
        width={props.width}
        height={props.height}
        src={`https://www.youtube.com/embed/${props.videoId}`}
      />
    );
  } else if (props.videoService === 'vimeo') {
    return (
      <iframe
        width={props.width}
        height={props.height}
        src={`https://player.vimeo.com/video/${props.videoId}`}
      />
    );
  } else if (props.videoService === 'facebook') {
    return (
      <iframe
        width={props.width}
        height={props.height}
        src={`https://www.facebook.com/video/embed?video_id=${props.videoId}`}
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

import { shallow } from 'enzyme';
import React from 'react';
import EmbeddedVideo from '../EmbeddedVideo';

global.embedVideoUrl = {
  facebook: 'https://www.facebook.com/video/embed?video_id=%s',
  vimeo: 'https://player.vimeo.com/video/%s',
  youtube: 'https://www.youtube.com/embed/%s',
};

describe('rendering', () => {
  it('renders correctly youtube embedded video', () => {
    const tree = shallow(
      <EmbeddedVideo
        videoService="youtube"
        videoId="youtube-video-id"
      />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly youtube embedded video with specific size', () => {
    const tree = shallow(
      <EmbeddedVideo
        videoService="youtube"
        videoId="youtube-video-id"
        width={1920}
        height={1080}
      />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly vimeo embedded video', () => {
    const tree = shallow(
      <EmbeddedVideo
        videoService="vimeo"
        videoId="vimeo-video-id"
      />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly vimeo embedded video with specific size', () => {
    const tree = shallow(
      <EmbeddedVideo
        videoService="vimeo"
        videoId="vimeo-video-id"
        width={1920}
        height={1080}
      />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly facebook embedded video', () => {
    const tree = shallow(
      <EmbeddedVideo
        videoService="facebook"
        videoId="facebook-video-id"
      />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly facebook embedded video with specific size', () => {
    const tree = shallow(
      <EmbeddedVideo
        videoService="facebook"
        videoId="facebook-video-id"
        width={1920}
        height={1080}
      />,
    );

    expect(tree).toMatchSnapshot();
  });
});

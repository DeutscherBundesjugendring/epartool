import { shallow } from 'enzyme';
import React from 'react';
import StaticMap from '../StaticMap';

global.osmStaticMapUrlTemplate = '%s?center=__latitude__,__longitude__&zoom=8&size=__width__x__height__&markers=__latitude__,__longitude__';

describe('rendering', () => {
  it('renders correctly with default size', () => {
    const tree = shallow(
      <StaticMap
        latitude={52.50828}
        longitude={13.38581}
      />,
    );

    expect(tree).toMatchSnapshot();
  });

  it('renders correctly with specified size', () => {
    const tree = shallow(
      <StaticMap
        latitude={52.50828}
        longitude={13.38581}
        width={150}
        height={100}
      />,
    );

    expect(tree).toMatchSnapshot();
  });
});

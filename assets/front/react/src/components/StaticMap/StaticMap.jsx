import React from 'react';

/* global osmStaticMapUrlTemplate */

const StaticMap = props => (
  <div className="map-static map-static-landscape">
    <img
      width={props.width}
      height={props.height}
      src={
        osmStaticMapUrlTemplate.replace(/__latitude__/g, props.latitude)
          .replace(/__longitude__/g, props.longitude)
          .replace('__width__', props.width)
          .replace('__height__', props.height)
      }
      alt={`GPS: ${props.latitude}, ${props.longitude}`}
    />
  </div>
);

StaticMap.defaultProps = {
  width: 240,
  height: 120,
};

StaticMap.propTypes = {
  latitude: React.PropTypes.number.isRequired,
  longitude: React.PropTypes.number.isRequired,
  width: React.PropTypes.number,
  height: React.PropTypes.number,
};

export default StaticMap;

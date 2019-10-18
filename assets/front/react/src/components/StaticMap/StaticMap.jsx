import React from 'react';
import PropTypes from 'prop-types';

/* global osmStaticMapUrlTemplate */

const StaticMap = ({
  height,
  latitude,
  longitude,
  width,
}) => (
  <div className="map-static map-static-landscape">
    <img
      width={width}
      height={height}
      src={
        osmStaticMapUrlTemplate.replace(/__latitude__/g, latitude)
          .replace(/__longitude__/g, longitude)
          .replace('__width__', width)
          .replace('__height__', height)
      }
      alt={`GPS: ${latitude}, ${longitude}`}
    />
  </div>
);

StaticMap.defaultProps = {
  height: 120,
  width: 240,
};

StaticMap.propTypes = {
  height: PropTypes.number,
  latitude: PropTypes.number.isRequired,
  longitude: PropTypes.number.isRequired,
  width: PropTypes.number,
};

export default StaticMap;

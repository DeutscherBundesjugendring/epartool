import React from 'react';
import PropTypes from 'prop-types';

const RaisedButton = ({
  disabled,
  id,
  label,
  onClick,
}) => {
  const linkClasses = 'btn btn-default btn-sm';

  return (
    <button
      id={id}
      type="button"
      onClick={() => onClick()}
      disabled={disabled}
      className={linkClasses}
    >
      {label}
    </button>
  );
};

RaisedButton.defaultProps = {
  disabled: false,
  id: null,
};

RaisedButton.propTypes = {
  disabled: PropTypes.bool,
  id: PropTypes.string,
  label: PropTypes.string.isRequired,
  onClick: PropTypes.func.isRequired,
};

export default RaisedButton;

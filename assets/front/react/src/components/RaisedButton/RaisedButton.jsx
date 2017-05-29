import React from 'react';


const RaisedButton = (props) => {
  const linkClasses = 'btn btn-default';

  return (
    <button
      onTouchTap={(e) => {
        e.stopPropagation();
        props.onTouchTap();
      }}
      disabled={props.disabled}
      className={linkClasses}
    >
      {props.label}
    </button>
  );
};

RaisedButton.defaultProps = {
  disabled: false,
};

RaisedButton.propTypes = {
  label: React.PropTypes.string.isRequired,
  onTouchTap: React.PropTypes.func.isRequired,
  disabled: React.PropTypes.bool,
};

export default RaisedButton;

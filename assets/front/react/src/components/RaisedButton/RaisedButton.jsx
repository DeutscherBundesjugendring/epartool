import React from 'react';


const RaisedButton = (props) => {
  const linkClasses = 'btn btn-default btn-sm';

  return (
    <button
      id={props.id}
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
  id: null,
};

RaisedButton.propTypes = {
  disabled: React.PropTypes.bool,
  id: React.PropTypes.string,
  label: React.PropTypes.string.isRequired,
  onTouchTap: React.PropTypes.func.isRequired,
};

export default RaisedButton;

import React from 'react';


const ThumbButton = (props) => {
  const linkClasses = 'link-alt link-unstyled';
  let iconClasses = 'glyphicon';

  if (props.type === 'like') {
    iconClasses += ' glyphicon-thumbs-up';
  }

  if (props.type === 'dislike') {
    iconClasses += ' glyphicon-thumbs-down';
  }

  return (
    <button onTouchTap={props.onTouchTap} className={linkClasses} disabled={props.disabled}>
      <span className={iconClasses} aria-hidden="true" />
    </button>
  );
};

ThumbButton.defaultProps = {
  disabled: false,
};

ThumbButton.propTypes = {
  type: React.PropTypes.oneOf(['like', 'dislike']).isRequired,
  onTouchTap: React.PropTypes.func.isRequired,
  disabled: React.PropTypes.bool,
};


export default ThumbButton;

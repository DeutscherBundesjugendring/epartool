const resolvePath = (currentPath) => {
  const pathStartsWith = currentPath.indexOf('/followup/');

  if (pathStartsWith !== -1) {
    const path = currentPath.substr(pathStartsWith).replace('/followup/', '');
    const params = path.split('/');

    // Show by contribution
    if (params.length === 7 && params[0] === 'show' && params[1] === 'kid' && params[5] === 'tid') {
      return ({
        type: 'followup-timeline',
        consultationId: parseInt(params[2], 10),
        followUpId: parseInt(params[6], 10),
        followUpType: 'contribution',
      });
    }

    // Show by snippet
    if (params.length === 5 && params[0] === 'show-by-snippet' && params[1] === 'kid' && params[3] === 'fid') {
      return ({
        type: 'followup-timeline',
        consultationId: parseInt(params[2], 10),
        followUpId: parseInt(params[4], 10),
        followUpType: 'snippet',
      });
    }

    // reaction_file page
    if (params.length === 3 && params[0] === 'index' && params[1] === 'kid') {
      return ({
        type: 'reactions-and-impact',
        consultationId: parseInt(params[2], 10),
      });
    }
  }

  return null;
};

export default resolvePath;

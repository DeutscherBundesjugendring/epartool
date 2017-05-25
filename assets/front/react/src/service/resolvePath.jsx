const resolvePath = (currentPath) => {
  if (currentPath.startsWith('/followup/')) {
    const path = currentPath.replace('/followup/', '');
    const params = path.split('/');

    if (params.length === 4 && params[0] === 'kid' && params[3] === 'fid') {
      return ({
        consultationId: params[1],
        followUpType: params[3],
        followUpId: params[3],
      });
    }
  }

  return null;
};

export default resolvePath;

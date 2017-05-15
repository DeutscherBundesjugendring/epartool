const resolvePath = (currentPath, followUpPath) => {
  if (currentPath.startsWith(followUpPath)) {
    const path = currentPath.replace(followUpPath, '');
    const params = path.split('/');

    if (params.length === 4 && params[0] === 'kid' && params[3] === 'fid') {
      return ({
        consultationId: params[1],
        followUpUd: params[3],
      });
    }
  }

  return null;
};

export default resolvePath;

const downloadFile = (url) => {
  const event = new MouseEvent('click', {
    view: window,
    bubbles: true,
    cancelable: true,
  });

  const link = document.createElement('a');
  link.href = url;
  link.download = url.split('/').pop();
  link.dispatchEvent(event);
};

export default downloadFile;

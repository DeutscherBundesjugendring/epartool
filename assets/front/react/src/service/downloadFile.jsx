const downloadFile = (url) => {
  const event = new MouseEvent('click', {
    bubbles: true,
    cancelable: true,
    view: window,
  });

  const link = document.createElement('a');
  link.href = url;
  link.download = url.split('/').pop();
  link.dispatchEvent(event);
};

export default downloadFile;

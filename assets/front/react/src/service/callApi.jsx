import 'whatwg-fetch';


const checkStatus = (response) => {
  if (!response.ok) {
    const error = new Error(response.statusText);
    error.response = response;

    throw error;
  }

  return response;
};

const parseJson = response => response.json();

const callApi = (url, method = 'GET') => (
  fetch(url, { method })
    .then(checkStatus)
    .then(parseJson)
);

export default callApi;

import callApi from './service/callApi';

/* global followupApiUrl */

export const fetchFollowUpElement = (type, followupId) => callApi(`${followupApiUrl}/element/${type}/${followupId}`);

export const fetchFollowUpElementChildren = (type, followupId) => callApi(`${followupApiUrl}/element/${type}/${followupId}/children`);

export const fetchFollowUpElementParents = (type, followupId) => callApi(`${followupApiUrl}/element/${type}/${followupId}/parents`);

export const fetchFollowUpDocument = (documentId) => callApi(`${followupApiUrl}/document/${documentId}`);

export const fetchFollowUpDocumentSnippets = (documentId) => callApi(`${followupApiUrl}/document/${documentId}/snippets`);

export const likeFollowUpDocumentSnippet = (snippetId) => callApi(`${followupApiUrl}/snippet/${snippetId}/like`, 'PUT');

export const dislikeFollowUpDocumentSnippet = (snippetId) => callApi(`${followupApiUrl}/snippet/${snippetId}/dislike`, 'PUT');

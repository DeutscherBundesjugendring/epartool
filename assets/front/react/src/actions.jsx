import callApi from './service/callApi';


/* global followupApiUrl */

export const fetchFollowUpElement = (type, followupId) =>
  callApi(`${followupApiUrl}/followup/element/${type}/${followupId}`);

export const fetchFollowUpElementChildren = (type, followupId) =>
  callApi(`${followupApiUrl}/followup/element/${type}/${followupId}/children`);

export const fetchFollowUpElementParents = (type, followupId) =>
  callApi(`${followupApiUrl}/followup/element/${type}/${followupId}/parents`);

export const fetchFollowUpDocument = documentId =>
  callApi(`${followupApiUrl}/followup/document/${documentId}`);

export const fetchFollowUpDocumentSnippets = documentId =>
  callApi(`${followupApiUrl}/followup/document/${documentId}/snippets`);

export const likeFollowUpDocumentSnippet = snippetId =>
  callApi(`${followupApiUrl}/followup/snippet/${snippetId}/like`, 'PUT');

export const dislikeFollowUpDocumentSnippet = snippetId =>
  callApi(`${followupApiUrl}/followup/snippet/${snippetId}/dislike`, 'PUT');

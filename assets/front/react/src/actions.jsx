import callApi from './service/callApi';


/* global followupApiUrl */

export const fetchFollowUpElement = (consultationId, followupId) =>
  callApi(`${followupApiUrl}/followup/element/${consultationId}/${followupId}`);

export const fetchFollowUpElementChildren = (consultationId, followupId) =>
  callApi(`${followupApiUrl}/followup/element/${consultationId}/${followupId}/children`);

export const fetchFollowUpElementParents = (consultationId, followupId) =>
  callApi(`${followupApiUrl}/followup/element/${consultationId}/${followupId}/parents`);

export const fetchFollowUpDocument = documentId =>
  callApi(`${followupApiUrl}/followup/document/${documentId}`);

export const fetchFollowUpDocumentSnippets = documentId =>
  callApi(`${followupApiUrl}/followup/document/${documentId}/snippets`);

export const likeFollowUpDocumentSnippet = snippetId =>
  callApi(`${followupApiUrl}/followup/snippet/${snippetId}/like`, 'PUT');

export const dislikeFollowUpDocumentSnippet = snippetId =>
  callApi(`${followupApiUrl}/followup/snippet/${snippetId}/dislike`, 'PUT');

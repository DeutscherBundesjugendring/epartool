import callApi from './service/callApi';


const url = 'api.example.com';

export const fetchFollowUpElement = (consultationId, followupId) =>
    callApi(`${url}/followup/element/${consultationId}/${followupId}`);

export const fetchFollowUpElementChildren = (consultationId, followupId) =>
    callApi(`${url}/followup/element/${consultationId}/${followupId}/children`);

export const fetchFollowUpElementParents = (consultationId, followupId) =>
    callApi(`${url}/followup/element/${consultationId}/${followupId}/parents`);

export const fetchFollowUpDocument = documentId =>
    callApi(`${url}/followup/document/${documentId}`);

export const fetchFollowUpDocumentSnippets = documentId =>
    callApi(`${url}/followup/document/${documentId}/snippets`);

export const likeFollowUpDocumentSnippet = snippetId =>
    callApi(`${url}/followup/snippet/${snippetId}/like`, 'PUT');

export const dislikeFollowUpDocumentSnippet = snippetId =>
    callApi(`${url}/followup/snippet/${snippetId}/dislike`, 'PUT');

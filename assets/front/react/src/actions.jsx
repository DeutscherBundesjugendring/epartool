import { apiUrl } from './config/config';
import callApi from './service/callApi';


export const fetchFollowUpElement = (consultationId, followupId) =>
    callApi(`${apiUrl}/followup/element/${consultationId}/${followupId}`);

export const fetchFollowUpElementChildren = (consultationId, followupId) =>
    callApi(`${apiUrl}/followup/element/${consultationId}/${followupId}/children`);

export const fetchFollowUpElementParents = (consultationId, followupId) =>
    callApi(`${apiUrl}/followup/element/${consultationId}/${followupId}/parents`);

export const fetchFollowUpDocument = documentId =>
    callApi(`${apiUrl}/followup/document/${documentId}`);

export const fetchFollowUpDocumentSnippets = documentId =>
    callApi(`${apiUrl}/followup/document/${documentId}/snippets`);

export const likeFollowUpDocumentSnippet = snippetId =>
    callApi(`${apiUrl}/followup/snippet/${snippetId}/like`, 'PUT');

export const dislikeFollowUpDocumentSnippet = snippetId =>
    callApi(`${apiUrl}/followup/snippet/${snippetId}/dislike`, 'PUT');

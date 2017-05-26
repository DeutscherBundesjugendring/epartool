<?php

/**
 * Class Api_FollowupController - method are mapped to routes in application.ini and don't use default Zend Router
 * mapping like module/controller/action[/param/value]
 */
class Api_FollowupController extends Dbjr_Api_BaseController
{
    const TYPE_SNIPPET = 'snippet';
    const TYPE_CONTRIBUTION = 'contribution';
    const TYPE_DOCUMENT = 'document';

    public function elementAction()
    {
        try {
            list($type, $id) = $this->getParameters(['type', 'id']);
            if ($type === self::TYPE_DOCUMENT) {
                $this->buildResponse(self::HTTP_STATUS_OK, [
                    'id' => $id,
                    'type' => $type,
                    'children_count' => $this->getChildrenCount($type, $id),
                    'parents_count' => 0,
                    'kid' => $this->getConsultationId($type, $id),
                    'data' => $this->getDocumentData($id),
                ]);
            } elseif ($type === self::TYPE_SNIPPET) {
                $this->buildResponse(self::HTTP_STATUS_OK, [
                    'id' => $id,
                    'type' => $type,
                    'children_count' => $this->getChildrenCount($type, $id),
                    'parents_count' => $this->getParentsCount($type, $id),
                    'kid' => $this->getConsultationId($type, $id),
                    'data' => $this->getSnippetData($id),
                ]);
            } elseif ($type === self::TYPE_CONTRIBUTION) {
                $this->buildResponse(self::HTTP_STATUS_OK, [
                    'id' => $id,
                    'type' => $type,
                    'children_count' => $this->getChildrenCount($type, $id),
                    'parents_count' => $this->getParentsCount($type, $id),
                    'kid' => $this->getConsultationId($type, $id),
                    'data' => $this->getContributionData($id),
                ]);
            } else {
                throw new Dbjr_Api_Exception(self::HTTP_STATUS_BAD_REQUEST, sprintf(
                    'Invalid type %s of element requested. Valid types are [%s]',
                    $type,
                    implode(', ', $this->getValidTypes())
                ));
            }
        } catch (Dbjr_Api_Exception $e) {
            $this->sendError($e->getHttpStatusCode(), $e->getMessage());
        } catch (Dbjr_Exception $e) {
            $this->sendError(self::HTTP_STATUS_SERVER_ERROR, 'Server error.');
        }
    }

    public function childrenAction()
    {
        try {
            list($type, $id) = $this->getParameters(['type', 'id']);
            if ($type === self::TYPE_DOCUMENT) {
                $this->buildResponse(self::HTTP_STATUS_OK, $this->getDocumentChildrenSnippetsData($id));
            } elseif ($type === self::TYPE_SNIPPET) {
                $this->buildResponse(self::HTTP_STATUS_OK, $this->getSnippetChildrenSnippetsData($id));
            } elseif ($type === self::TYPE_CONTRIBUTION) {
                $this->buildResponse(self::HTTP_STATUS_OK, array_merge(
                    $this->getContributionsChildrenContributionsData($id),
                    $this->getContributionsChildrenSnippetsData($id)
                ));
            } else {
                throw new Dbjr_Api_Exception(self::HTTP_STATUS_BAD_REQUEST, sprintf(
                    'Invalid type %s of element requested. Valid types are [%s]',
                    $type,
                    implode(', ', $this->getValidTypes())
                ));
            }
        } catch (Dbjr_Api_Exception $e) {
            $this->sendError($e->getHttpStatusCode(), $e->getMessage());
        } catch (Dbjr_Exception $e) {
            $this->sendError(self::HTTP_STATUS_SERVER_ERROR, 'Server error.');
        }
    }

    public function parentsAction()
    {
        try {
            list($type, $id) = $this->getParameters(['type', 'id']);
            if ($type === self::TYPE_DOCUMENT) {
                throw new Dbjr_Api_Exception(self::HTTP_STATUS_BAD_REQUEST, sprintf(
                    'Document element cannot have parent elements.',
                    $type,
                    implode(', ', $this->getValidTypes())
                ));
            } elseif ($type === self::TYPE_SNIPPET) {
                $this->buildResponse(self::HTTP_STATUS_OK, $this->getSnippetParentsAllData($id));
            } elseif ($type === self::TYPE_CONTRIBUTION) {
                $this->buildResponse(self::HTTP_STATUS_OK, $this->getContributionsParentsContributionsData($id));
            } else {
                throw new Dbjr_Api_Exception(self::HTTP_STATUS_BAD_REQUEST, sprintf(
                    'Invalid type %s of element requested. Valid types are [%s]',
                    $type,
                    implode(', ', $this->getValidTypes())
                ));
            }
        } catch (Dbjr_Api_Exception $e) {
            $this->sendError($e->getHttpStatusCode(), $e->getMessage());
        } catch (Dbjr_Exception $e) {
            $this->sendError(self::HTTP_STATUS_SERVER_ERROR, 'Server error.');
        }
    }

    public function documentAction()
    {
        try {
            list($documentId) = $this->getParameters(['id']);
            $this->buildResponse(self::HTTP_STATUS_OK, $this->getDocumentData($documentId));
        } catch (Dbjr_Api_Exception $e) {
            $this->sendError($e->getHttpStatusCode(), $e->getMessage());
        } catch (Dbjr_Exception $e) {
            $this->sendError(self::HTTP_STATUS_SERVER_ERROR, 'Server error.');
        }
    }

    /**
     * Returns snippets of the document by $documentId
     */
    public function snippetsAction()
    {
        try {
            list($documentId) = $this->getParameters(['id']);
            $this->buildResponse(self::HTTP_STATUS_OK, $this->getSnippetsOfDocumentData($documentId));
        } catch (Dbjr_Api_Exception $e) {
            $this->sendError($e->getHttpStatusCode(), $e->getMessage());
        } catch (Dbjr_Exception $e) {
            $this->sendError(self::HTTP_STATUS_SERVER_ERROR, 'Server error.');
        }
    }

    public function likeAction()
    {
        $this->like('lkyea');
    }

    public function dislikeAction()
    {
        $this->like('lknay');
    }

    /**
     * @param string $property
     */
    private function like($property)
    {
        try {
            if (!$this->getRequest()->isPut()) {
                throw new Dbjr_Api_Exception(self::HTTP_STATUS_BAD_REQUEST, 'Unsupported method. Only PUT is allowed.');
            }
            list($snippetId) = $this->getParameters(['id']);
            $result = (new Model_Followups())->supportById($snippetId, $property);
            $this->buildResponse(self::HTTP_STATUS_OK, [$property => (string) $result]);
        } catch (Dbjr_Api_Exception $e) {
            $this->sendError($e->getHttpStatusCode(), $e->getMessage());
        } catch (Dbjr_Exception $e) {
            $this->sendError(self::HTTP_STATUS_SERVER_ERROR, 'Server error.');
        }
    }

    /**
     * @param int $documentId
     * @return array
     */
    private function getDocumentChildrenSnippetsData($documentId)
    {
        $snippets = (new Model_FollowupFiles())->getFollowupsById($documentId);
        $result = [];

        foreach ($snippets as $snippet) {
            $result[] = [
                'id' => $snippet['fid'],
                'type' => self::TYPE_SNIPPET,
                'children_count' => $this->getChildrenCount(self::TYPE_SNIPPET, $snippet['fid']),
                'parents_count' => $this->getParentsCount(self::TYPE_SNIPPET, $snippet['fid']),
                'kid' => $this->getConsultationId(self::TYPE_SNIPPET, $snippet['fid']),
                'data' => $this->getSnippetData($snippet['fid']),
            ];
        }

        return $result;
    }

    /**
     * @param int $contributionId
     * @return array
     */
    private function getContributionsChildrenContributionsData($contributionId)
    {
        $contributions = (new Model_Inputs())->getChildrenByParentId($contributionId);
        $result = [];

        foreach ($contributions as $contribution) {
            $result[] = [
                'id' => $contribution['tid'],
                'type' => self::TYPE_CONTRIBUTION,
                'children_count' => $this->getChildrenCount(self::TYPE_CONTRIBUTION, $contribution['tid']),
                'parents_count' => $this->getParentsCount(self::TYPE_CONTRIBUTION, $contribution['tid']),
                'kid' => $this->getConsultationId(self::TYPE_CONTRIBUTION, $contribution['tid']),
                'data' => $this->getContributionData($contribution['tid']),
            ];
        }

        return $result;
    }

    /**
     * @param int $contributionId
     * @return array
     */
    private function getContributionsChildrenSnippetsData($contributionId)
    {
        $snippets = (new Model_Inputs())->getFollowups($contributionId);
        $result = [];

        foreach ($snippets as $snippet) {
            $result[] = [
                'id' => $snippet['fid'],
                'type' => self::TYPE_SNIPPET,
                'children_count' => $this->getChildrenCount(self::TYPE_SNIPPET, $snippet['fid']),
                'parents_count' => $this->getParentsCount(self::TYPE_SNIPPET, $snippet['fid']),
                'kid' => $this->getConsultationId(self::TYPE_SNIPPET, $snippet['fid']),
                'data' => $this->getsnippetData($snippet['fid']),
            ];
        }

        return $result;
    }

    /**
     * @param int $contributionId
     * @return array
     */
    private function getContributionsParentsContributionsData($contributionId)
    {
        $contributions = (new Model_Inputs())->getParentsByChildId($contributionId);
        $result = [];

        foreach ($contributions as $contribution) {
            $result[] = [
                'id' => $contribution['tid'],
                'type' => self::TYPE_CONTRIBUTION,
                'children_count' => $this->getChildrenCount(self::TYPE_CONTRIBUTION, $contribution['tid']),
                'parents_count' => $this->getParentsCount(self::TYPE_CONTRIBUTION, $contribution['tid']),
                'kid' => $this->getConsultationId(self::TYPE_CONTRIBUTION, $contribution['tid']),
                'data' => $this->getContributionData($contribution['tid']),
            ];
        }

        return $result;
    }

    /**
     * @param int $snippetId
     * @return array
     */
    private function getSnippetChildrenSnippetsData($snippetId)
    {
        $snippets = (new Model_FollowupsRef())->getRelatedFollowupByFid($snippetId);
        $result = [];

        foreach ($snippets as $snippet) {
            $result[] = [
                'id' => $snippet['fid_ref'],
                'type' => self::TYPE_SNIPPET,
                'children_count' => $this->getChildrenCount(self::TYPE_SNIPPET, $snippet['fid_ref']),
                'parents_count' => $this->getParentsCount(self::TYPE_SNIPPET, $snippet['fid_ref']),
                'kid' => $this->getConsultationId(self::TYPE_SNIPPET, $snippet['fid_ref']),
                'data' => $this->getContributionData($snippet['fid_ref']),
            ];
        }

        return $result;
    }

    /**
     * @param int $snippetId
     * @return array
     */
    private function getSnippetParentsAllData($snippetId)
    {
        $elements = (new Model_Followups())->getRelated($snippetId);
        $result = [];

        foreach ($elements['inputs'] as $contribution) {
            $result[] = [
                'id' => $contribution['tid'],
                'type' => self::TYPE_CONTRIBUTION,
                'children_count' => $this->getChildrenCount(self::TYPE_CONTRIBUTION, $contribution['tid']),
                'parents_count' => $this->getParentsCount(self::TYPE_CONTRIBUTION, $contribution['tid']),
                'kid' => $this->getConsultationId(self::TYPE_CONTRIBUTION, $contribution['tid']),
                'data' => $this->getContributionData($contribution['tid']),
            ];
        }

        foreach ($elements['snippets'] as $snippet) {
            $result[] = [
                'id' => $snippet['fid'],
                'type' => self::TYPE_SNIPPET,
                'children_count' => $this->getChildrenCount(self::TYPE_SNIPPET, $snippet['fid']),
                'parents_count' => $this->getParentsCount(self::TYPE_SNIPPET, $snippet['fid']),
                'kid' => $this->getConsultationId(self::TYPE_SNIPPET, $snippet['fid']),
                'data' => $this->getSnippetData($snippet['fid']),
            ];
        }

        foreach ($elements['followups'] as $document) {
            $result[] = [
                'id' => $document['ffid'],
                'type' => self::TYPE_DOCUMENT,
                'children_count' => $this->getChildrenCount(self::TYPE_DOCUMENT, $document['ffid']),
                'parents_count' => 0,
                'kid' => $this->getConsultationId(self::TYPE_DOCUMENT, $document['ffid']),
                'data' => $this->getDocumentData($document['ffid']),
            ];
        }

        return $result;
    }

    /**
     * @param int $documentId
     * @return array
     * @throws Dbjr_Api_Exception
     */
    private function getDocumentData($documentId)
    {
        $document = (new Model_FollowupFiles())->getById($documentId, true);
        if (empty($document)) {
            throw new Dbjr_Api_Exception(
                self::HTTP_STATUS_NOT_FOUND,
                sprintf('Document with id %d was not found', $documentId)
            );
        }

        return [
            'ffid' => $document['ffid'],
            'kid' => $document['kid'],
            'titl' => $document['titl'],
            'who' => $document['when'],
            'is_only_month_year_showed' => $document['is_only_month_year_showed'],
            'ref_doc' => $document['ref_doc'],
            'ref_view' => $document['ref_view'],
            'gfx_who' => $document['gfx_who'],
            'type' => $document['type']
        ];
    }

    /**
     * @param int $snippetId
     * @return array
     * @throws Dbjr_Api_Exception
     */
    private function getSnippetData($snippetId)
    {
        $snippet = (new Model_Followups())->getById($snippetId);
        if (empty($snippet)) {
            throw new Dbjr_Api_Exception(
                self::HTTP_STATUS_NOT_FOUND,
                sprintf('Snippet with id %d was not found', $snippetId)
            );
        }

        return [
            'fid' => $snippet['fid'],
            'embed' => $snippet['embed'],
            'expl' => $snippet['expl'],
            'ffid' => $snippet['ffid'],
            'lkyea' => $snippet['lkyea'],
            'lknay' => $snippet['lknay'],
            'type' => $snippet['type'],
            'gfx_who' => (new Model_FollowupFiles())->getById($snippet['ffid'])['gfx_who'],
        ];
    }

    /**
     * @param int $contributionId
     * @return array
     * @throws Dbjr_Api_Exception
     */
    private function getContributionData($contributionId)
    {
        $contribution = (new Model_Inputs())->getById($contributionId);
        if (empty($contribution)) {
            throw new Dbjr_Api_Exception(
                self::HTTP_STATUS_NOT_FOUND,
                sprintf('Contribution with id %d was not found', $contributionId)
            );
        }

        return [
            'tid' => $contribution['tid'],
            'qi' => $contribution['qi'],
            'thes' => $contribution['thes'],
            'uid' => $contribution['uid'],
            'when' => $contribution['when'],
            'is_votable' => $contribution['is_votable'],
            'spprts' => $contribution['spprts'],
            'votes' => $contribution['votes'],
            'place' => $contribution['place'],
            'expl' => $contribution['expl'],
            'video_service' => $contribution['video_service'],
            'video_id' => $contribution['video_id'],
        ];
    }

    /**
     * @param int $documentId
     * @return array
     */
    private function getSnippetsOfDocumentData($documentId)
    {
        $result = (new Model_FollowupFiles())->getFollowupsById($documentId);
        if ($result instanceof Zend_Db_Table_Rowset) {
            return $result->toArray();
        }

        return $result;
    }

    /**
     * @param string $type
     * @param int $id
     * @return int
     * @throws Exception
     */
    private function getChildrenCount($type, $id)
    {
        if ($type === self::TYPE_DOCUMENT) {
            return (new Model_FollowupFiles())->getFollowupsCountById($id);
        } elseif ($type === self::TYPE_SNIPPET) {
            return (new Model_FollowupsRef())->getRelatedFollowupCountByFid($id);
        } elseif ($type === self::TYPE_CONTRIBUTION) {
            $contributionModel = new Model_Inputs();
            return $contributionModel->getFollowupsCount($id) + $contributionModel->getChildrenCountByParentId($id);
        } else {
            throw new Exception(sprintf('%s is invalid type of element.', $type));
        }
    }

    /**
     * @param string $type
     * @param int $id
     * @return int
     * @throws Exception
     */
    private function getParentsCount($type, $id)
    {
        if ($type === self::TYPE_DOCUMENT) {
            throw new Exception(sprintf('Element of type %s cannot have parents.', $type));
        } elseif ($type === self::TYPE_SNIPPET) {
            return (new Model_Followups())->getRelatedCount($id);
        } elseif ($type === self::TYPE_CONTRIBUTION) {
            return (new Model_Inputs())->getParentsCountByChildId($id);
        } else {
            throw new Exception(sprintf('%s is invalid type of element.', $type));
        }
    }

    /**
     * @return array
     */
    private function getValidTypes()
    {
        return [self::TYPE_CONTRIBUTION, self::TYPE_DOCUMENT, self::TYPE_SNIPPET];
    }

    /**
     * @param string $type
     * @param int $id
     * @return int
     * @throws Exception
     */
    private function getConsultationId($type, $id)
    {
        if ($type === self::TYPE_DOCUMENT) {
            return (int) $this->getDocumentData($id)['kid'];
        } elseif ($type === self::TYPE_SNIPPET) {
            return (new Model_Followups())->getConsultationIdBySnippet($id);
        } elseif ($type === self::TYPE_CONTRIBUTION) {
            return (new Model_Inputs())->getConsultationIdByContribution($id);;
        } else {
            throw new Exception(sprintf('%s is invalid type of element.', $type));
        }
    }
}

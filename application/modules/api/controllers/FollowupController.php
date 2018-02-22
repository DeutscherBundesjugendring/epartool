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
            $data = [
                'id' => (int) $id,
                'type' => $type,
                'children_count' => $this->getChildrenCount($type, [$id])[$id],
                'parents_count' => $this->getParentsCount($type, [$id])[$id],
            ];

            if ($type === self::TYPE_DOCUMENT) {
                $document = $this->getDocumentData([$id])[$id];
                $this->buildResponse(self::HTTP_STATUS_OK, $data + [
                    'kid' => $document['kid'],
                    'data' => $document,
                ]);
            } elseif ($type === self::TYPE_SNIPPET) {
                $snippet = $this->getSnippetData([$id])[$id];
                $this->buildResponse(self::HTTP_STATUS_OK, $data + [
                    'kid' => $snippet['kid'],
                    'data' => $snippet,
                ]);
            } elseif ($type === self::TYPE_CONTRIBUTION) {
                $contribution = $this->getContributionData([$id])[$id];
                $this->buildResponse(self::HTTP_STATUS_OK, $data + [
                    'kid' => $contribution['kid'],
                    'data' => $contribution,
                ]);
            }

            throw new Dbjr_Api_Exception(self::HTTP_STATUS_BAD_REQUEST, sprintf(
                'Invalid type %s of element requested. Valid types are [%s]',
                $type,
                implode(', ', $this->getValidTypes())
            ));
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
                $snippets = (new Model_FollowupFiles())->getFollowupsById($id);
                $relatedSnippetsIds = (new Model_FollowupsRef())->getRelatedFollowupsByFfid($id);
                $this->buildResponse(self::HTTP_STATUS_OK, $this->buildSnippetDetailsData(
                    array_merge($snippets->toArray(), $this->getSnippetData($relatedSnippetsIds))
                ));
            } elseif ($type === self::TYPE_SNIPPET) {
                $snippetsRaw = (new Model_FollowupsRef())->getRelatedFollowupByFid($id);
                $snippets = [];
                foreach ($snippetsRaw as $snippet) {
                    $snippets[] = ['fid' => $snippet['fid_ref']];
                }
                $this->buildResponse(self::HTTP_STATUS_OK, $this->buildSnippetDetailsData($snippets));
            } elseif ($type === self::TYPE_CONTRIBUTION) {
                $inputModel = new Model_Inputs();
                $contribData = $this->buildContributionDetailsData($inputModel->getChildrenByParentId($id));
                $snippetData = $this->buildSnippetDetailsData($inputModel->getFollowups($id));
                $this->buildResponse(self::HTTP_STATUS_OK, array_merge($contribData, $snippetData));
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
                $contributions = (new Model_Inputs())->getParentsByChildId($id);
                $this->buildResponse(self::HTTP_STATUS_OK, $this->buildContributionDetailsData($contributions));
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
            $this->buildResponse(self::HTTP_STATUS_OK, $this->getDocumentData([$documentId])[$documentId]);
        } catch (Dbjr_Api_Exception $e) {
            $this->sendError($e->getHttpStatusCode(), $e->getMessage());
        } catch (Dbjr_Exception $e) {
            $this->sendError(self::HTTP_STATUS_SERVER_ERROR, 'Server error.');
        }
    }

    /**
     * Returns reaction_snippets of the document by $documentId
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
            $this->buildResponse(self::HTTP_STATUS_OK, [
                $property => (new Model_Followups())->supportById($snippetId, $property)
            ]);
        } catch (Dbjr_Api_Exception $e) {
            $this->sendError($e->getHttpStatusCode(), $e->getMessage());
        } catch (Dbjr_Exception $e) {
            $this->sendError(self::HTTP_STATUS_SERVER_ERROR, 'Server error.');
        }
    }

    /**
     * @param array $origSnippets
     * @return array
     */
    private function buildSnippetDetailsData(array $origSnippets)
    {
        $snippetIds = $this->getIds($origSnippets, 'fid');
        $childrenCounts = $this->getChildrenCount(self::TYPE_SNIPPET, $snippetIds);
        $parentCounts = $this->getParentsCount(self::TYPE_SNIPPET, $snippetIds);
        $snippets = $this->getSnippetData($snippetIds);

        $result = [];
        foreach ($snippets as $snippet) {
            $result[] = [
                'id' => (int) $snippet['fid'],
                'type' => self::TYPE_SNIPPET,
                'children_count' => $childrenCounts[$snippet['fid']],
                'parents_count' => $parentCounts[$snippet['fid']],
                'kid' => $snippets[$snippet['fid']]['kid'],
                'data' => $snippets[$snippet['fid']],
            ];
        }

        return $result;
    }

    /**
     * @param array $origContributions
     * @return array
     */
    private function buildContributionDetailsData(array $origContributions)
    {
        $contributionIds = $this->getIds($origContributions, 'tid');
        $childrenCounts = $this->getChildrenCount(self::TYPE_CONTRIBUTION, $contributionIds);
        $parentCounts = $this->getParentsCount(self::TYPE_CONTRIBUTION, $contributionIds);
        $contributions = $this->getContributionData($contributionIds);

        $result = [];
        foreach ($contributions as $contribution) {
            $result[] = [
                'id' => (int) $contribution['tid'],
                'type' => self::TYPE_CONTRIBUTION,
                'children_count' => $childrenCounts[$contribution['tid']],
                'parents_count' => $parentCounts[$contribution['tid']],
                'kid' => $contributions[$contribution['tid']]['kid'],
                'data' => $contributions[$contribution['tid']],
            ];
        }

        return $result;
    }

    /**
     * @param array $origDocuments
     * @return array
     */
    private function buildDocumentDetailsData(array $origDocuments)
    {
        $documentIds = $this->getIds($origDocuments, 'ffid');
        $childrenCounts = $this->getChildrenCount(self::TYPE_DOCUMENT, $documentIds);
        $parentCounts = $this->getParentsCount(self::TYPE_DOCUMENT, $documentIds);
        $documents = $this->getDocumentData($documentIds);

        $result = [];
        foreach ($documents as $document) {
            $result[] = [
                'id' => (int) $document['ffid'],
                'type' => self::TYPE_DOCUMENT,
                'children_count' => $childrenCounts[$document['ffid']],
                'parents_count' => $parentCounts[$document['ffid']],
                'kid' => $documents[$document['ffid']]['kid'],
                'data' => $documents[$document['ffid']],
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
        return array_merge(
            $this->buildContributionDetailsData($elements['inputs']),
            $this->buildSnippetDetailsData($elements['snippets']),
            $this->buildDocumentDetailsData($elements['followups'])
        );
    }

    /**
     * @param array $documentIds
     * @return array
     * @throws Dbjr_Api_Exception
     */
    private function getDocumentData(array $documentIds)
    {
        if (!$documentIds) {
            return [];
        }

        $documentModel = new Model_FollowupFiles();
        $rows = $documentModel
            ->select()
            ->from(['c' => $documentModel->info($documentModel::NAME)])
            ->where('ffid IN (?)', implode(',', $documentIds))
            ->query()
            ->fetchAll();

        if (empty($rows)) {
            throw new Dbjr_Api_Exception(
                self::HTTP_STATUS_NOT_FOUND,
                sprintf('No documents were found for ids: [%s]', implode($documentIds))
            );
        }

        $documents = [];
        foreach ($rows as $row) {
            $documents[$row['ffid']] = [
                'ffid' => (int)$row['ffid'],
                'kid' => (int)$row['kid'],
                'titl' => $row['titl'],
                'when' => $this->convertDateTime($row['when']),
                'is_only_month_year_showed' => (bool)$row['is_only_month_year_showed'],
                'ref_doc' => $this->getImageBasePath($row['kid']) . $row['ref_doc'],
                'ref_view' => $row['ref_view'],
                'gfx_who' => $this->getImageBasePath($row['kid']) . $row['gfx_who'],
                'type' => $row['type'],
                'who' => $row['who'],
            ];
        }

        return $documents;
    }

    /**
     * @param array $snippetIds
     * @throws Dbjr_Api_Exception
     * @return array
     */
    private function getSnippetData(array $snippetIds)
    {
        if (!$snippetIds) {
            return [];
        }

        $snippetModel = new Model_Followups();
        $rows = $snippetModel
            ->select()
            ->setIntegrityCheck(false)
            ->from(['fs' => $snippetModel->info($snippetModel::NAME)])
            ->join(['ff' => 'fowup_fls'], 'ff.ffid = fs.ffid', ['gfx_who', 'kid', 'titl'])
            ->where('fid IN (?)', $snippetIds)
            ->query()
            ->fetchAll();

        if (empty($rows)) {
            throw new Dbjr_Api_Exception(
                self::HTTP_STATUS_NOT_FOUND,
                sprintf('No snippets were found fo ids: [%s]', implode($snippetIds))
            );
        }

        $snippets = [];
        foreach ($rows as $row) {
            $snippets[$row['fid']] = [
                'fid' => (int) $row['fid'],
                'expl' => $row['expl'],
                'ffid' => (int) $row['ffid'],
                'lkyea' => (int) $row['lkyea'],
                'lknay' => (int) $row['lknay'],
                'type' => $row['type'],
                'kid' => $row['kid'],
                'document' => [
                    'gfx_who' => $this->getImageBasePath($row['kid']) . $row['gfx_who'],
                    'title' => $row['titl'],
                ]
            ];
        }

        return $snippets;
    }

    /**
     * @param array $contributionIds
     * @throws Dbjr_Api_Exception
     * @return array
     */
    private function getContributionData(array $contributionIds)
    {
        if (!$contributionIds) {
            return [];
        }

        $contribModel = new Model_Inputs();
        $rows = $contribModel
            ->select()
            ->setIntegrityCheck(false)
            ->from(['c' => $contribModel->info($contribModel::NAME)])
            ->join(['q' => 'quests'], 'c.qi = q.qi', ['q', 'kid', 'nr', 'location_enabled'])
            ->where('tid IN (?)', $contributionIds)
            ->query()
            ->fetchAll();
        if (empty($rows)) {
            throw new Dbjr_Api_Exception(
                self::HTTP_STATUS_NOT_FOUND,
                sprintf('No contributions were found for ids: [%s]', implode($contributionIds))
            );
        }

        $contributions = [];
        foreach ($rows as $row) {
            $votingResults = (new Model_Votes())->getResultsValues($row['kid'], $row['qi']);
            $place = 0;
            if (isset($votingResults['votings'])) {
                foreach ($votingResults['votings'] as $key => $contributionVotingResult) {
                    if ((int) $row['tid'] === (int) $contributionVotingResult['tid']) {
                        $place = (int) $key + 1;
                    }
                }
            }

            $contributions[$row['tid']] = [
                'tid' => (int) $row['tid'],
                'qi' => (int) $row['qi'],
                'question' => $row['q'],
                'question_number' => $row['nr'],
                'location_enabled' => (bool) $row['location_enabled'],
                'latitude' => (float) $row['latitude'],
                'longitude' => (float) $row['longitude'],
                'thes' => $row['thes'],
                'uid' => (int) $row['uid'],
                'when' => $this->convertDateTime($row['when']),
                'is_votable' => (bool) $row['is_votable'],
                'spprts' => (int) $row['spprts'],
                'votes' => (int) $row['votes'],
                'place' => $place,
                'expl' => $row['expl'],
                'kid' => $row['kid'],
                'video_service' => $row['video_service'],
                'video_id' => $row['video_id'],
            ];
        }

        return $contributions;
    }

    /**
     * @param int $documentId
     * @return array
     */
    private function getSnippetsOfDocumentData($documentId)
    {
        $result = (new Model_FollowupFiles())->getFollowupsById($documentId);
        if ($result instanceof Zend_Db_Table_Rowset) {
            $resultArray = $result->toArray();
        } else {
            $resultArray = $result;
        }

        $snippetIds = $this->getIds($resultArray, 'fid');
        $childrenCounts = $this->getChildrenCount(self::TYPE_SNIPPET, $snippetIds);
        $parentCounts = $this->getParentsCount(self::TYPE_SNIPPET, $snippetIds);

        foreach ($resultArray as $key => $snippet) {
            $resultArray[$key]['fid'] = (int) $resultArray[$key]['fid'];
            $resultArray[$key]['docorg'] = (int) $resultArray[$key]['docorg'];
            $resultArray[$key]['ffid'] = (int) $resultArray[$key]['ffid'];
            $resultArray[$key]['lknay'] = (int) $resultArray[$key]['lknay'];
            $resultArray[$key]['lkyea'] = (int) $resultArray[$key]['lkyea'];
            $resultArray[$key]['parents_count'] = $parentCounts[$snippet['fid']];
            $resultArray[$key]['children_count'] = $childrenCounts[$snippet['fid']];
        }

        return $resultArray;
    }

    /**
     * @param string $type
     * @param array $ids
     * @return array
     * @throws Exception
     */
    private function getChildrenCount($type, array $ids)
    {
        if ($type === self::TYPE_DOCUMENT) {
            $snippetModel = new Model_Followups();
            $select = $snippetModel
                ->select()
                ->from(
                    ['s' => $snippetModel->info($snippetModel::NAME)],
                    ['ffid', new Zend_Db_Expr('COUNT(*) as count')]
                );

            $snippetCount = $this->getCounts($select, $ids, 'ffid');

            $followupRefModel = new Model_FollowupsRef();
            $select = $followupRefModel->select()
                ->from(
                    ['r' => $followupRefModel->info($followupRefModel::NAME)],
                    ['ffid', new Zend_Db_Expr('COUNT(*) as count')]
                );

            $relatedSnippetCount = $this->getCounts($select, $ids, 'ffid');

            $result = [];
            foreach ($snippetCount as $ffid => $count) {
                $result[$ffid] = $count + $relatedSnippetCount[$ffid];
            }

            return $result;
        }

        if ($type === self::TYPE_SNIPPET) {
            $refModel = new Model_FollowupsRef();
            $select = $refModel
                ->select()
                ->from(
                    ['rr' => $refModel->info($refModel::NAME)],
                    ['fid', new Zend_Db_Expr('COUNT(*) as count')]
                );

            return $this->getCounts($select, $ids, 'fid');
        }

        if ($type === self::TYPE_CONTRIBUTION) {
            $refModel = new Model_FollowupsRef();
            $select = $refModel
                ->select()
                ->from(
                    ['rr' => $refModel->info($refModel::NAME)],
                    ['tid', new Zend_Db_Expr('COUNT(*) as count')]
                );
            $reactionCounts = $this->getCounts($select, $ids, 'tid');


            $contribRelModel = new Model_InputRelations();
            $select = $contribRelModel
                ->select()
                ->from(
                    ['cr' => $contribRelModel->info($contribRelModel::NAME)],
                    ['parent_id', new Zend_Db_Expr('COUNT(*) as count')]
                );
            $contributionCounts = $this->getCounts($select, $ids, 'parent_id');

            $result = [];
            foreach ($reactionCounts as $contribId => $count) {
                $result[$contribId] = $count + $contributionCounts[$contribId];
            }

            return $result;
        }

        throw new Exception(sprintf('%s is invalid type of element.', $type));
    }

    /**
     * @param string $type
     * @param array $ids
     * @return array
     * @throws Exception
     */
    private function getParentsCount($type, array $ids)
    {
        if ($type === self::TYPE_DOCUMENT) {
            return $this->zeroFillCounts([], $ids);
        }

        if ($type === self::TYPE_SNIPPET) {
            $refModel = new Model_FollowupsRef();
            $select = $refModel
                ->select()
                ->from(
                    ['rr' => $refModel->info($refModel::NAME)],
                    ['fid_ref', new Zend_Db_Expr('COUNT(*) as count')]
                );

            return $this->getCounts($select, $ids, 'fid_ref');
        }

        if ($type === self::TYPE_CONTRIBUTION) {
            $contribRelModel = new Model_InputRelations();
            $select = $contribRelModel
                ->select()
                ->from(
                    ['cr' => $contribRelModel->info($contribRelModel::NAME)],
                    ['child_id', new Zend_Db_Expr('COUNT(*) as count')]
                );

            return $this->getCounts($select, $ids, 'child_id');
        }

        throw new Exception(sprintf('%s is invalid type of element.', $type));
    }

    /**
     * @param Zend_Db_Select $select
     * @param array $ids
     * @param string $idCol
     * @return array
     */
    private function getCounts(Zend_Db_Select $select, array $ids, $idCol)
    {
        if (!$ids) {
            return [];
        }

        $counts = [];
        $rows = $select
            ->where($idCol . ' IN (?)', $ids)
            ->group($idCol)
            ->query()
            ->fetchAll();

        foreach ($rows as $row) {
            $counts[$row[$idCol]] = $row['count'];
        }

        return $this->zeroFillCounts($counts, $ids);
    }

    /**
     * @param array $counts
     * @param array $ids
     * @return array
     */
    private function zeroFillCounts(array $counts, array $ids)
    {
        $newCounts = [];
        foreach ($ids as $id) {
            if (!isset($counts[$id])) {
                $newCounts[$id] = 0;
                continue;
            }
            $newCounts[$id] = $counts[$id];
        }

        return $newCounts;
    }

    /**
     * @return array
     */
    private function getValidTypes()
    {
        return [self::TYPE_CONTRIBUTION, self::TYPE_DOCUMENT, self::TYPE_SNIPPET];
    }

    /**
     * @param array $entities
     * @param string $idCol
     * @return array
     */
    private function getIds(array $entities, $idCol)
    {
        $ids = [];
        foreach ($entities as $entity) {
            $ids[] = $entity[$idCol];
        };

        return $ids;
    }

    /**
     * @param string $dateTime
     * @return string
     */
    private function convertDateTime($dateTime)
    {
        return DateTime::createFromFormat('Y-m-d H:i:s', $dateTime)->format(DateTime::RFC3339);
    }

    /**
     * @param int $kid
     * @return string
     */
    private function getImageBasePath($kid)
    {
        return $this->view->baseUrl() . MEDIA_URL . '/consultations/' . $kid . '/';
    }
}

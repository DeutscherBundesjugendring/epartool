<?php

/**
 * Class Admin_View_Helper_PageTitle
 */
class Application_View_Helper_PageTitle extends Zend_View_Helper_Abstract
{
    const GLOBAL_STATIC_PAGE = 'page_type_global_static';
    const STATIC_PAGE = 'page_type_static';
    const CONSULTATION_PAGE = 'page_type_consultation';
    const INFO_PAGE = 'page_type_info';
    const CONTRIBUTIONS_PAGE = 'page_type_contributions';
    const CONTRIBUTIONS_BY_QUESTION_PAGE = 'page_type_contributions_by_question';
    const CONTRIBUTION_PAGE = 'page_type_contribution';
    const VOTING_PAGE = 'page_type_boting';
    const FOLLOWUP_PAGE = 'page_type_follow_up';
    const FOLLOWUP_DETAIL_PAGE = 'page_type_follow_up_detail';

    const POSTFIX = 'ePartool';

    /**
     * @var string
     */
    private $globalSiteTitle;

    /**
     * @var Zend_Translate
     */
    private $translator;

    public function __construct()
    {
        $this->translator = Zend_Registry::get('Zend_Translate');
        $this->globalSiteTitle = (new Model_Parameter())->getAsArray()['site.title'];
    }

    /**
     * @param string|null $type
     * @param Zend_Db_Table_Rowset|array|string|null $entity
     * @return string
     */
    public function pageTitle($type = null, $entity = null)
    {
        if (self::GLOBAL_STATIC_PAGE === $type) {
            return $this->globalStaticPage($entity);
        } elseif(self::CONSULTATION_PAGE === $type) {
            return $this->consultation($entity);
        } elseif(self::INFO_PAGE === $type) {
            return $this->info($entity);
        } elseif(self::CONTRIBUTIONS_PAGE === $type) {
            return $this->contributions($entity);
        } elseif(self::CONTRIBUTIONS_BY_QUESTION_PAGE === $type) {
            return $this->contributionByQuestion($entity);
        } elseif(self::CONTRIBUTION_PAGE === $type) {
            return $this->contribution($entity);
        } elseif(self::VOTING_PAGE === $type) {
            return $this->voting($entity);
        } elseif(self::FOLLOWUP_PAGE === $type) {
            return $this->followUp($entity);
        } elseif(self::FOLLOWUP_DETAIL_PAGE === $type) {
            return $this->followUpDetail($entity);
        } elseif(self::STATIC_PAGE === $type) {
            return $this->staticTitle($entity);
        }

        return $this->universal();
    }

    /**
     * @param Zend_Db_Table_Rowset|array|null $globalArticle
     * @throws Exception
     * @return string
     */
    private function globalStaticPage($globalArticle)
    {
        if (!$globalArticle || !isset($globalArticle['desc'])) {
            throw new Exception('No global article entity with title was not specified');
        }

        return sprintf(
            '%s – %s – %s',
            $globalArticle['desc'],
            $this->globalSiteTitle,
            self::POSTFIX
        );
    }

    /**
     * @param string $title
     * @return string
     */
    private function staticTitle($title)
    {
        if ($title) {
            return sprintf('%s – %s – %s', $title, $this->globalSiteTitle, self::POSTFIX);
        }

        return $this->universal();
    }

    /**
     * @param Zend_Db_Table_Rowset|array|null $consultation
     * @throws \Exception
     * @return string
     */
    private function consultation($consultation)
    {
        if (!$consultation || !isset($consultation['titl_short'])) {
            throw new Exception('No consultation entity with title was not specified');
        }

        return sprintf(
            '%s – %s – %s',
            $consultation['titl_short'],
            $this->globalSiteTitle,
            self::POSTFIX
        );
    }

    /**
     * @param Zend_Db_Table_Rowset|array|null $article
     * @throws \Exception
     * @return string
     */
    private function info($article)
    {
        if (!$article || !isset($article['desc'])) {
            throw new Exception('No article entity with title was not specified');
        }

        return sprintf(
            '%s – %s – %s – %s',
            $article['desc'],
            $this->getConsultationTitleBy($article),
            $this->globalSiteTitle,
            self::POSTFIX
        );
    }

    /**
     * @param Zend_Db_Table_Rowset|array|null $consultation
     * @throws \Exception
     * @return string
     */
    private function contributions($consultation)
    {
        if (!$consultation || !isset($consultation['titl_short'])) {
            throw new Exception('No consultation entity with title was not specified');
        }

        return sprintf(
            '%s – %s – %s – %s',
            $this->translator->translate('Contributions'),
            $consultation['titl_short'],
            $this->globalSiteTitle,
            self::POSTFIX
        );
    }

    /**
     * @param Zend_Db_Table_Rowset|array|null $question
     * @throws \Exception
     * @return string
     */
    private function contributionByQuestion($question)
    {
        if (!$question || !isset($question['q'])) {
            throw new Exception('No question entity with title was not specified');
        }

        return sprintf(
            '%s – %s – %s – %s – %s',
            $question['q'],
            $this->translator->translate('Contributions'),
            $this->getConsultationTitleBy($question),
            $this->globalSiteTitle,
            self::POSTFIX
        );
    }

    /**
     * @param Zend_Db_Table_Rowset|array|null $question
     * @throws \Exception
     * @return string
     */
    private function contribution($question)
    {
        if (!$question || !isset($question['q'])) {
            throw new Exception('No question entity with title was not specified');
        }

        return sprintf(
            '%s – %s – %s – %s – %s',
            $question['q'],
            $this->translator->translate('Contribution'),
            $this->getConsultationTitleBy($question),
            $this->globalSiteTitle,
            self::POSTFIX
        );
    }

    /**
     * @param Zend_Db_Table_Rowset|array|null $consultation
     * @throws \Exception
     * @return string
     */
    private function voting($consultation)
    {
        if (!$consultation || !isset($consultation['titl_short'])) {
            throw new Exception('No consultation entity with title was not specified');
        }

        return sprintf(
            '%s – %s – %s – %s',
            $this->translator->translate('Voting'),
            $consultation['titl_short'],
            $this->globalSiteTitle,
            self::POSTFIX
        );
    }

    /**
     * @param Zend_Db_Table_Rowset|array|null $consultation
     * @throws \Exception
     * @return string
     */
    private function followUp($consultation)
    {
        if (!$consultation || !isset($consultation['titl_short'])) {
            throw new Exception('No consultation entity with title was not specified');
        }

        return sprintf(
            '%s – %s – %s – %s',
            $this->translator->translate('Follow-up'),
            $consultation['titl_short'],
            $this->globalSiteTitle,
            self::POSTFIX
        );
    }

    /**
     * @param Zend_Db_Table_Rowset|array|null $question
     * @throws \Exception
     * @return string
     */
    private function followUpDetail($question)
    {
        if (!$question || !isset($question['q'])) {
            throw new Exception('No question entity with title was not specified');
        }

        return sprintf(
            '%s – %s – %s – %s – %s',
            $question['q'],
            $this->translator->translate('Follow-up'),
            $this->getConsultationTitleBy($question),
            $this->globalSiteTitle,
            self::POSTFIX
        );
    }

    /**
     * @return string
     */
    private function universal()
    {
        return sprintf('%s – %s', $this->globalSiteTitle, self::POSTFIX);
    }

    /**
     * @param Zend_Db_Table_Rowset|array $entity
     * @throws Exception
     * @return string
     */
    private function getConsultationTitleBy($entity)
    {
        if (!isset($entity['kid'])) {
            throw new Exception('Consultation ID in the entity specified for the page title is not defined');
        }
        $consultation = (new Model_Consultations())->find($entity['kid'])->current();

        if ($consultation === null) {
            throw new Exception('Consultation ID for the entity specified for the page title was not found');
        }

        return $consultation['titl_short'];
    }
}

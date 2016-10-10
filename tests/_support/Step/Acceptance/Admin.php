<?php

namespace Step\Acceptance;

use Component\LoginComponent;
use Page\Admin\Consultation\ListConsultationPage;
use Page\Admin\Consultation\NewConsultationPage;
use Page\Admin\Question\CreateQuestionPage;
use Page\Admin\Question\ListQuestionPage;
use Page\Front\Index\IndexPage;

class Admin extends \AcceptanceTester
{
    const ADMIN_DEFAULT_EMAIL = 'email@email.com';
    const ADMIN_DEFAULT_PASSWORD = 'password';

    const CONSULTATION_CONTRIBUTION_DURATION_DAYS = 10;
    const CONSULTATION_VOTING_DURATION_DAYS = 10;
    const DAY = 86400;

    public function login()
    {
        $this->amOnPage(IndexPage::$url);
        $this->click(LoginComponent::$loginButtonId);
        $this->fillField(LoginComponent::$fieldUsernameId, self::ADMIN_DEFAULT_EMAIL);
        $this->fillField(LoginComponent::$fieldPasswordId, self::ADMIN_DEFAULT_PASSWORD);
        $this->click(LoginComponent::$loginSubmitButtonId);
        $this->dontSee(LoginComponent::$loginFailedMessage);
        $this->dontSee(LoginComponent::$loginInvalidCredentialsMessage);
        $this->see(self::ADMIN_DEFAULT_EMAIL);
    }

    public function logout()
    {
        $this->click(LoginComponent::$menuDropDown);
        $this->waitForElementVisible(LoginComponent::$userDropDown);
        $this->click(LoginComponent::$userDropDown);
        $this->waitForElementVisible(LoginComponent::$logOffIcon);
        $this->click(LoginComponent::$logOffLink);
    }

    /**
     * @param null $title
     * @param null $shortTitle
     */
    public function createConsultation($title = null, $shortTitle = null)
    {
        $this->amOnPage(NewConsultationPage::$url);
        $this->fillField(
            NewConsultationPage::$fieldTitle,
            $title === null ? 'consultation 1 long title' : $title
        );
        $this->fillField(
            NewConsultationPage::$fieldShortTitle,
            $shortTitle === null ? 'consultation 1 short title' : $shortTitle
        );
        $this->fillField(NewConsultationPage::$fieldContributionPhaseStart, date('Y-m-d H:i:s', time()));
        $this->fillField(
            NewConsultationPage::$fieldContributionPhaseEnd,
            date('Y-m-d H:i:s', time() + self::CONSULTATION_CONTRIBUTION_DURATION_DAYS * self::DAY)
        );
        $this->uncheckOption(NewConsultationPage::$checkboxAllowSupport);
        $this->uncheckOption(NewConsultationPage::$checkboxAllowDiscussion);
        $this->fillField(NewConsultationPage::$fieldVotingPhaseStart, date('Y-m-d H:i:s', time()));
        $this->fillField(
            NewConsultationPage::$fieldVotingPhaseEnd,
            date('Y-m-d H:i:s', time() + self::CONSULTATION_VOTING_DURATION_DAYS * self::DAY)
        );
        $this->checkOption(NewConsultationPage::$checkboxMakePublic);
        $this->click(NewConsultationPage::$saveButton);
        $this->see(NewConsultationPage::$messageCreated);
        
        
    }

    /**
     * @param null $title
     */
    public function createQuestion($title = null)
    {
        $this->amOnPage(ListConsultationPage::$url);
        $this->click(ListConsultationPage::$ListQuestionsLink);
        $this->click(ListQuestionPage::$addNewButton);
        $this->fillField(
            CreateQuestionPage::$fieldQuestionId,
            $title === null ? 'Question 1 in consultation' : $title
        );
        $this->click(CreateQuestionPage::$buttonSaveId);
        $this->see(CreateQuestionPage::$messageCreated);
    }
}

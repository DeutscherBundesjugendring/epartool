<?php

namespace acceptance\Admin\Voting;

use Page\Admin\Consultation\ListConsultationPage;
use Page\Admin\Voting\VotingPreparePage;
use Step\Acceptance\Admin;
use Step\Acceptance\FileSystem;
use Step\Acceptance\User;

class VotingPrepareCest
{
    const CONSULTATION_TITLE = 'consultation 1';
    const QUESTION_TITLE = 'question1';
    const CONTRIBUTION_TEXT_1 = 'Contribution test 1';
    const CONTRIBUTION_TEXT_2 = 'Contribution test 2';
    const CONTRIBUTION_TEXT_3 = 'Contribution test 3';
    const CONTRIBUTION_TEXT_4 = 'Contribution test 4';

    public function _before(Admin $I, User $user, FileSystem $fileSystem)
    {
        $fileSystem->backupMedia();
        $I->login();
        $I->createConsultation(self::CONSULTATION_TITLE);
        $I->createQuestion(self::QUESTION_TITLE);
        $I->logout();
        $user->createContribution(self::CONTRIBUTION_TEXT_1);
        $user->createContribution(self::CONTRIBUTION_TEXT_2);
        $user->createContribution(self::CONTRIBUTION_TEXT_3);
        $I->login();
        $I->amOnPage(ListConsultationPage::$url);
        $I->click(VotingPreparePage::$menuButton);
        $I->click(self::QUESTION_TITLE);
    }

    public function _after(Admin $I, FileSystem $fileSystem)
    {
        $I->logout();
        $fileSystem->restoreMedia();
    }

    public function testCreateAndRemoveRelatedContribution(\AcceptanceTester $I)
    {
        $I->click(VotingPreparePage::$buttonAddRelated);
        $I->click(sprintf(VotingPreparePage::$contributionCheckboxWithValue, 2));
        $I->click(sprintf(VotingPreparePage::$contributionCheckboxWithValue, 3));
        $I->click(VotingPreparePage::$buttonSaveRelated);
        $I->seeElement(sprintf(VotingPreparePage::$buttonRemoveRelated, 2, 1));
        $I->seeElement(sprintf(VotingPreparePage::$buttonRemoveRelated, 3, 1));

        $I->click(sprintf(VotingPreparePage::$buttonRemoveRelated, 2, 1));
        $I->click(VotingPreparePage::$buttonConfirmationYes);
        $I->dontSeeElement(sprintf(VotingPreparePage::$buttonRemoveRelated, 2, 1));
        $I->seeElement(sprintf(VotingPreparePage::$buttonRemoveRelated, 3, 1));
    }

    public function testSplitContribution(\AcceptanceTester $I)
    {
        $I->click(VotingPreparePage::$buttonSplit);
        $I->fillField(VotingPreparePage::$textContributionId, self::CONTRIBUTION_TEXT_4);
        $I->click(VotingPreparePage::$buttonSaveSplitted);
        $I->see(VotingPreparePage::$messageContributionCreated);
        $I->click(VotingPreparePage::$linkCancel);
        $I->seeElement(sprintf(VotingPreparePage::$buttonRemoveRelated, 4, 1));
        $I->dontSeeElement(sprintf(VotingPreparePage::$buttonRemoveRelated, 1, 4));
    }

    public function testMergeContributions(\AcceptanceTester $I)
    {
        $I->click(sprintf(VotingPreparePage::$contributionCheckboxWithValue, 1));
        $I->click(sprintf(VotingPreparePage::$contributionCheckboxWithValue, 2));
        $I->click(VotingPreparePage::$buttonMerge);
        $I->fillField(VotingPreparePage::$textContributionId, self::CONTRIBUTION_TEXT_4);
        $I->click(VotingPreparePage::$radioAdminConfirmedId);
        $I->click(VotingPreparePage::$radioUserConfirmedId);
        $I->click(VotingPreparePage::$radioVotingEnableId);
        $I->click(VotingPreparePage::$buttonSaveMerged);
        $I->seeElement(sprintf(VotingPreparePage::$buttonRemoveRelated, 4, 1));
        $I->seeElement(sprintf(VotingPreparePage::$buttonRemoveRelated, 4, 2));
    }

    public function testCopyContribution(\AcceptanceTester $I)
    {
        $I->click(VotingPreparePage::$buttonCopy);
        $I->click(VotingPreparePage::$buttonSaveCopied);
        $I->see(VotingPreparePage::$messageContributionCopied);
        $I->click(VotingPreparePage::$linkCancel);
        $I->dontSeeElement(sprintf(VotingPreparePage::$buttonRemoveRelated, 1, 4));
        $I->seeElement(sprintf(VotingPreparePage::$buttonRemoveRelated, 4, 1));
    }
}

<?php

namespace Step\Acceptance;

use Page\Front\Contribution\ConfirmContributionPage;
use Page\Front\Contribution\CreateContributionPage;
use Page\Front\Index\IndexPage;
use Phinx\Db\Table\Index;

class User extends \AcceptanceTester
{
    public function createContribution($title = null)
    {
        // refactor > only browser interaction
        $this->amOnPage(IndexPage::$url);
        $this->click(IndexPage::$contributionsBookMark);
        $this->click(IndexPage::$allContributionsLink);
        
        $this->fillField(
            CreateContributionPage::$fieldContributionText, 
            $title === null ? 'Text of test contribution in question ID' : $title
        );
        $this->click(CreateContributionPage::$buttonFinishedId);

        $this->see(ConfirmContributionPage::$messageInfo);
        $this->fillField(ConfirmContributionPage::$fieldEmailId, 'test@example.com');
        $this->checkOption(ConfirmContributionPage::$checkboxAgree);
        $this->click(ConfirmContributionPage::$buttonSendId);
    }
}

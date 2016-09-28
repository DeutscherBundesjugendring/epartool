<?php

namespace acceptance\Front\Index;

use Page\Front\Index\IndexPage;

class IndexControllerCest
{
    public function _before(\AcceptanceTester $I)
    {
        $I->amOnPage(IndexPage::$url);
    }

    public function _after(\AcceptanceTester $I)
    {
    }

    public function tryToTest(\AcceptanceTester $I)
    {
        $I->see(IndexPage::$title);
    }
}

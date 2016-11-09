<?php

use Component\LoginComponent;
use Page\Admin\Consultation\NewConsultationPage;
use Page\Admin\Question\CreateQuestionPage;
use Page\Admin\Question\ListQuestionPage;
use Page\Front\Contribution\ConfirmContributionPage;
use Page\Front\Contribution\CreateContributionPage;
use Page\Front\Index\IndexPage;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;
}

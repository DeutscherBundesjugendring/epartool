<?php

namespace Page\Admin\Voting;

class VotingPreparePage
{
    public static $url = '/admin/votingprepare/index/kid/%d';
    public static $menuButton = 'Voting';
    public static $buttonSplit = '.glyphicon-resize-full';
    public static $buttonCopy = '.glyphicon-th-large';
    public static $radioUserConfirmedId = '#user_conf-c';
    public static $radioAdminConfirmedId = '#block-n';
    public static $radioVotingEnableId = '#vot-y';
    public static $buttonMerge = 'button[name="merge"]';
    public static $buttonConfirmationYes = 'Yes';
    public static $buttonAddRelated = '.item-action .glyphicon-plus-sign';
    public static $buttonSaveRelated = 'button[value="saveRelated"]';
    public static $buttonSaveSplitted = 'input[value="Save and continue"]';
    public static $buttonSaveMerged = 'input[value="Save and return to index"]';
    public static $buttonSaveCopied = 'input[value="Save and return to index"]';
    public static $linkCancel = 'Cancel';
    public static $buttonRemoveRelated = 'button.item-action-danger[value="%d-%d"]';
    public static $textContributionId = '#thes';
    public static $contributionCheckboxWithValue = '.checkbox input[value="%d"]';
    public static $title = 'DBJR TOOL Admin';

    public static $messageContributionCreated = 'New contribution has been created.';
    public static $messageContributionCopied = 'Contribution has been copied. This is the copy.';
}

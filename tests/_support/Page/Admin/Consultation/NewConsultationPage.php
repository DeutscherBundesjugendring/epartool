<?php

namespace Page\Admin\Consultation;

class NewConsultationPage
{
    public static $url = '/admin/consultation/new';
    
    public static $fieldTitle = '#titl';
    public static $fieldShortTitle = '#titl_short';
    public static $fieldContributionPhaseStart = '#inp_fr';
    public static $fieldContributionPhaseEnd = '#inp_to';
    public static $fieldVotingPhaseStart = '#vot_fr';
    public static $fieldVotingPhaseEnd = '#vot_to';
    public static $checkboxAllowSupport = '#is_support_phase_showed';
    public static $checkboxAllowDiscussion = '#is_discussion_active';
    public static $checkboxMakePublic = '#public';
    public static $saveButton = '#submit';
    
    public static $messageCreated = 'New consultation has been created.';
}

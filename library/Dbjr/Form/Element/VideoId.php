<?php

class Dbjr_Form_Element_VideoId extends Dbjr_Form_Element_Text
{
    public function init()
    {
        $this->addValidator(new Dbjr_Validate_VideoValidator());
    }
}

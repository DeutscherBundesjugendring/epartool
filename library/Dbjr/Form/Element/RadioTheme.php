<?php

class Dbjr_Form_Element_RadioTheme extends Dbjr_Form_Element_Radio
{
    public function init()
    {
        $themes = (new Model_Theme())->fetchAll();
        $themesOptions = [];
        $colorConfig = [];
        foreach ($themes as $theme) {
            $themesOptions[$theme['id']] = $theme['name'];
            $colorConfig[$theme['id']] = json_encode([
                'color_headings' => $theme['color_headings'],
                'color_frame_background' => $theme['color_headings'],
                'color_active_link' => $theme['color_active_link'],
            ]);
        }
        $this->setMultioptions($themesOptions)->setOptions(['color_config' => $colorConfig]);
    }
}

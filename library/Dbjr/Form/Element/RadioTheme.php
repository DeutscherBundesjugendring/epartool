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
                'color_primary' => '#'. $theme['color_primary'],
                'color_accent_1' => '#' . $theme['color_accent_1'],
                'color_accent_2' => '#'. $theme['color_accent_2'],
            ]);
        }
        $this->setMultioptions($themesOptions)->setOptions(['color_config' => $colorConfig]);
    }
}

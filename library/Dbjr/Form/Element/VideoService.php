<?php

class Dbjr_Form_Element_VideoService extends Dbjr_Form_Element_Select
{
    public function init()
    {
        $project = (new Model_Projects())->find((new Zend_Registry())->get('systemconfig')->project)->current();
        $videoServiceOptions = [];
        $urls = [];
        foreach (['youtube' => 'Youtube', 'vimeo' => 'Vimeo', 'facebook' => 'Facebook'] as $service => $name) {
            if ($project['video_' . $service . '_enabled']) {
                $videoServiceOptions[$service] = $name;
                $urls[$service] = sprintf(Zend_Registry::get('systemconfig')->video->url->$service->format->link, '');
            }
        }
        $this->setMultioptions($videoServiceOptions)->setOptions(['data-url' => json_encode($urls)]);
    }
}

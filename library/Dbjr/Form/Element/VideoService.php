<?php

class Dbjr_Form_Element_VideoService extends Dbjr_Form_Element_Select
{
    private function getVideoServices(): array
    {
        $webServiceConf = Zend_Registry::get('systemconfig')->webservice;
        return [
            'youtube' => [
                'label' => 'YouTube',
                'isActiveInConfig' => true, // does not need any configuration
            ],
            'vimeo' => [
                'label' => 'Vimeo',
                'isActiveInConfig' => $webServiceConf
                    && $webServiceConf->vimeo
                    && $webServiceConf->vimeo->accessToken,
            ],
            'facebook' => [
                'label' => 'Facebook',
                'isActiveInConfig' => $webServiceConf
                    && $webServiceConf->facebook
                    && $webServiceConf->facebook->appSecret
                    && $webServiceConf->facebook->appId,
            ],
        ];
    }

    public function init()
    {
        $project = (new Model_Projects())->find((new Zend_Registry())->get('systemconfig')->project)->current();
        $videoServiceOptions = [];
        $urls = [];
        foreach ($this->getVideoServices() as $service => $props) {
            if ($project['video_' . $service . '_enabled'] && $props['isActiveInConfig']) {
                $videoServiceOptions[$service] = $props['label'];
                $urls[$service] = sprintf(Zend_Registry::get('systemconfig')->video->url->$service->format->link, '');
            }
        }
        $this->setMultioptions($videoServiceOptions)->setOptions(['data-url' => json_encode($urls)]);
    }
}

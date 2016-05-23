<?php

/**
 * Class Admin_View_Helper_EmbeddedVideo
 */
class Application_View_Helper_EmbeddedVideo extends Zend_View_Helper_Abstract
{

    /**
     * @return string
     */
    public function embeddedVideo($service, $id)
    {
        if (method_exists($this, $service)) {
            return $this->$service($id);
        }
        
        throw new Exception('Service ' . $service . ' for embedding video is not defined.');
    }
    
    /**
     * @param string $id
     * @return string
     */
    private function facebook($id)
    {
        return '<div class="embed-responsive embed-responsive-16by9">
                <div
                    class="fb-video"
                    data-href="https://www.facebook.com/facebook/videos/' . $id . '/"
                    data-width="520"
                    data-show-text="false"
                ></div>
            </div>';
    }
    
    /**
     * @param string $id
     * @return string
     */
    private function vimeo($id)
    {
        return '<div class="embed-responsive embed-responsive-16by9">
                <iframe
                    src="https://player.vimeo.com/video/' . $id . '?title=0&byline=0&portrait=0&badge=0"
                    width="640"
                    height="480"
                    frameborder="0"
                    webkitallowfullscreen
                    mozallowfullscreen
                    allowfullscreen
                ></iframe>
            </div>';
    }
    
    /**
     * @param string $id
     * @return string
     */
    private function youtube($id)
    {
        return '<div class="embed-responsive embed-responsive-16by9">
                <iframe
                    id="ytplayer"
                    type="text/html"
                    width="640"
                    height="480"
                    src="https://www.youtube.com/embed/' . $id . '?autoplay=0"
                    frameborder="0"
                ></iframe>
            </div>';
    }
}

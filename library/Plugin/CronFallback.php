<?php

class Plugin_CronFallback extends Zend_Controller_Plugin_Abstract
{
    const RUN_CRON_PROBABILITY = 5; // %

    /**
     * @param \Zend_Controller_Request_Abstract $request
     * @throws \Zend_Exception
     * @throws Exception
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ($request->getControllerName() === 'cron') {
            return;
        }

        $cronConfig = Zend_Registry::get('systemconfig')->cron;
        if ($cronConfig && $cronConfig->key) {
            return;
        }

        if (!$this->getTrueWithProbability(self::RUN_CRON_PROBABILITY)) {
            return;
        }

        if (!Service_Cron::enterCriticalSection()) {
            return;
        }
        try {
            $this->post_async(Zend_Registry::get('baseUrl') . '/cron/fallback', ['h' => Service_Cron::getHash()]);
        } catch (Exception $e) {
            if ($e->getCode() !== Service_Cron::E_CODE_HASH_IN_USE) {
                Service_Cron::leaveCriticalSection();

                throw $e;
            }
        }
        Service_Cron::leaveCriticalSection();
    }

    /**
     * @param int $probability
     * @return bool
     */
    private function getTrueWithProbability(int $probability): bool
    {
        return rand(0, 100) <= $probability;
    }

    /**
     * @param string $url
     * @param array $params
     */
    private function post_async(string $url, array $params)
    {
        $post_params = [];
        foreach ($params as $key => &$val) {
            if (is_array($val)) $val = implode(',', $val);
            $post_params[] = $key.'='.urlencode($val);
        }
        $post_string = implode('&', $post_params);

        $parts=parse_url($url);

        $fp = fsockopen(
            $parts['host'],
            isset($parts['port']) ? $parts['port'] : 80,
            $errno,
            $errstr,
            30
        );

        $out = "POST ".$parts['path']." HTTP/1.1\r\n";
        $out.= "Host: ".$parts['host']."\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        // @codingStandardsIgnoreLine
        $out.= "Content-Length: ".strlen($post_string)."\r\n";
        $out.= "Connection: Close\r\n\r\n";
        if (isset($post_string)) $out.= $post_string;

        fwrite($fp, $out);
        fclose($fp);
    }
}

<?php

class Service_Article
{
    const BASE_URL_PLACEHOLDER = '{{BASE_URL}}';

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * Service_Article constructor.
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $text
     * @return string
     */
    public function placeholderToBasePath($text)
    {
        return str_replace(
            self::BASE_URL_PLACEHOLDER . '/media/',
            $this->baseUrl . '/media/',
            $text
        );
    }

    /**
     * @param string $text
     * @return string
     */
    public function basePathToPlaceholder($text)
    {
        return str_replace(
            $this->baseUrl . '/media/',
            self::BASE_URL_PLACEHOLDER . '/media/',
            $text
        );
    }
}

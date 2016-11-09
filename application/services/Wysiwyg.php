<?php

class Service_Wysiwyg
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
            self::BASE_URL_PLACEHOLDER . MEDIA_URL . '/',
            $this->baseUrl . MEDIA_URL . '/',
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
            $this->baseUrl . MEDIA_URL . '/',
            self::BASE_URL_PLACEHOLDER . MEDIA_URL . '/',
            $text
        );
    }
}

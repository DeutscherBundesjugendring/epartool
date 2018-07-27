<?php

class Service_User
{
    /**
     * @param string $email
     * @return string
     */
    public static function generateName(string $email): string
    {
        $localPart = explode('@', $email)[0];
        $nameArr = [$localPart];

        foreach (['.', '_', '-'] as $separator) {
            if (mb_strpos($localPart, $separator) !== false) {
                $nameArr = explode($separator, $localPart);
                break;
            }
        }

        $nameArr = array_map(function ($el) {
            return ucfirst($el);
        }, $nameArr);

        return implode(' ', $nameArr);
    }
}

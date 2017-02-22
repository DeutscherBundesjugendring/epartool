<?php

class Service_BoolValue
{
    /**
     * @param int|string $value
     * @return bool|null
     */
    public function convertForDb($value)
    {
        if ($value === '') {
            return null;
        }

        return (int) $value;
    }
}

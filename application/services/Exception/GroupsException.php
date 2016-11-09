<?php

class Service_Exception_GroupsException extends Dbjr_Exception
{
    /**
     * @var array
     */
    private $interval;

    /**
     * @var string
     */
    private $intervalGroup;

    /**
     * @param array $interval
     * @return $this
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;
        return $this;
    }

    /**
     * @return array
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @return bool
     */
    public function hasInterval()
    {
        return is_array($this->interval);
    }

    /**
     * @return string
     */
    public function getIntervalGroup()
    {
        return $this->intervalGroup;
    }

    /**
     * @param string $intervalGroup
     * @return Service_Exception_GroupsException
     */
    public function setIntervalGroup($intervalGroup)
    {
        $this->intervalGroup = $intervalGroup;
        return $this;
    }
}
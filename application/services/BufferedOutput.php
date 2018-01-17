<?php

use Symfony\Component\Console\Output\Output;

class Service_BufferedOutput extends Output
{
    protected $buffer;

    public function doWrite($message, $newline)
    {
        $this->buffer .= $message . ($newline ? '<br />' : '');
    }

    public function getBuffer()
    {
        return $this->buffer;
    }
}

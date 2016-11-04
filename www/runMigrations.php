<?php

// Scrpit taken from: https://github.com/robmorgan/phinx/issues/137#issuecomment-26220408


// Comment out to run migrations.
die('Locked over ftp. Open me or go away.');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
use Symfony\Component\Console\Output\Output;

class BufferedOutput extends Output
{
    protected $buffer;

    public function doWrite($message, $newline)
    {
        echo $message;
        $this->buffer .= $message. ($newline? PHP_EOL: '');
    }

    public function getBuffer()
    {
        return $this->buffer;
    }
}

$input = new \Symfony\Component\Console\Input\StringInput('migrate -c ../application/configs/phinx.local.yml');
$output = new BufferedOutput;

$app = new Phinx\Console\PhinxApplication();
$app->setAutoExit(false);

var_dump($app->run($input, $output));
var_dump($output->getBuffer());

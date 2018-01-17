<?php

class Service_PhinxMigrate {

    const COMMAND = 'migrate -c ../application/configs/phinx.local.yml -e %s';

    /**
     * @var Service_BufferedOutput
     */
    private $output;

    /**
     * @var string
     */
    private $environment;

    public function __construct($environment) {
        $this->environment = $environment;
        $this->output = new Service_BufferedOutput();
    }

    public function run() {
        $app = new Phinx\Console\PhinxApplication();
        $app->setAutoExit(false);
        $app->run(
            new \Symfony\Component\Console\Input\StringInput(sprintf(self::COMMAND, $this->environment)),
            $this->output
        );
    }

    public function getOutput() {
        return $this->output->getBuffer();
    }
}

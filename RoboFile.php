<?php

use  JoeStewart\RoboDrupalVM\Task\Vm;
use  Mediacurrent\CiScripts\Task\Project;

class RoboFile extends \Robo\Tasks
{
    use \Mediacurrent\CiScripts\Task\loadTasks;
    use \Mediacurrent\CiScripts\Command\Project;

    private $vm;
    private $configuration;

    private $drupalvm_package;

    public function __construct() {

      $this->drupalvm_package = 'mediacurrent/mis_vagrant';

      $this->vm = New Vm();
      $this->configuration = $this->vm->configuration;
    }

    public function test()
    {
        $this->stopOnFail(true);
        $this->taskPHPUnit()
            ->option('disallow-test-output')
            ->option('report-useless-tests')
            ->option('strict-coverage')
            ->option('-v')
            ->option('-d error_reporting=-1')
            ->arg('tests')
            ->run();
    }
}

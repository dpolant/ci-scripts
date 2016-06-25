<?php

namespace Mediacurrent\CiScripts\Task;


use Robo\Result;
use Robo\Common\ResourceExistenceChecker;
use Robo\Common\Timer;
use Robo\Common\TaskIO;

class ProjectInit extends \Mediacurrent\CiScripts\Task\Base
{
    use Timer;
    use ResourceExistenceChecker;
    use \JoeStewart\RoboDrupalVM\Task\loadTasks;

    public function vmInit($drupalvm_package) {
        $this->taskVmInit()
            ->drupalvmPackage($drupalvm_package)
            ->configFile()
            ->vagrantFile($drupalvm_package)
            ->run();
        return $this;
    }

    public function testsInit() {
        $this->taskRsync()
            ->fromPath($this->getVendorDir() . '/mediacurrent/ci-tests/tests')
            ->toPath($this->getProjectRoot())
            ->archive()
            ->verbose()
            ->exclude('.gitignore')
            ->option('--ignore-existing')
            ->recursive()
            ->run();

        if(!is_file($this->getProjectRoot() . '/tests/behat/behat.local.yml')) {
            $this->taskFileSystemStack()
                ->copy($this->getProjectRoot() . '/tests/behat/behat.local.yml.example', $this->getProjectRoot() . '/tests/behat/behat.local.yml')
                ->run();
                $this->taskReplaceInFile($this->getProjectRoot() . '/tests/behat/behat.local.yml')
                ->from('base_url:')
                ->to('base_url: http://' . $this->configuration['vagrant_hostname'])
                ->run();

        }
        return $this;
    }

    public function vagrantInit($vagrant_hostname = null, $vagrant_ip = null) {
            if($vagrant_hostname) {
            $this->taskReplaceInFile($this->getVagrantConfig())
                ->from('example.mcdev')
                ->to($vagrant_hostname)
                ->run();
            $this->taskReplaceInFile($this->getVagrantConfig())
                ->from('example_mcdev')
                ->to(str_replace('.', '_', $vagrant_hostname))
                ->run();
            }
            if($vagrant_ip) {
            $this->taskReplaceInFile($this->getVagrantConfig())
                ->from('192.168.50.4')
                ->to($vagrant_ip)
                ->run();
            }
            return $this;
        }

    /**
     * @return Result
     */
    public function run()
    {
        $this->startTimer();
        $this->stopTimer();
        return new Result(
            $this,
            0,
            'ProjectInit',
            ['time' => $this->getExecutionTime()]
        );

    }
}

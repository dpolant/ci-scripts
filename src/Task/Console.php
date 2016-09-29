<?php

namespace Mediacurrent\CiScripts\Task;

use Robo\Result;
use Robo\Exception\TaskException;

class Console extends \Mediacurrent\CiScripts\Task\Base
{
    use \Robo\Task\Base\loadTasks;
    use \Robo\Task\Remote\loadTasks;

    protected $arg;
    protected $console_command;
    protected $console_options;
    protected $root;
    protected $uri;

    public function arg($arg = null) {
        $this->arg = $arg;

        return $this;
    }

    public function consoleCommand($console_command = null) {
        $this->console_command = $console_command;

        return $this;
    }

    public function consoleOptions($console_options = null) {
        $this->console_options = $console_options;

        return $this;
    }

    public function getCommand($pathToInstallDir = null, $uri = null) {

        if (!$pathToInstallDir) {
             $pathToInstallDir = $this->configuration['drupal_composer_install_dir'];
        }

        $console = $pathToInstallDir . '/bin/drupal';
        $root = $pathToInstallDir . '/web';
        if(!$uri) {
            $uri = 'http://' . $this->configuration['vagrant_hostname'];
        }
        $command = $console . ' --uri=' . $uri . ' --root=' . $root . ' ' . $this->console_command;
        if($this->arg) {
            $command .= ' ' . $this->arg;
        }
        if($this->console_options) {
            $command .= ' ' . $this->console_options;
        }

        return $command;
    }

    /**
     * @return Result
     */
    public function run()
    {

        $command = $this->getCommand();

        $this->printTaskInfo($command);
        if($this->useVagrant()) {
            $this->taskSshExec($this->configuration['vagrant_hostname'], 'vagrant')
                ->remoteDir($this->configuration['drupal_composer_install_dir'] . '/web/')
                ->exec($command)
                ->identityFile('~/.vagrant.d/insecure_private_key')
                ->run();
        }
        else {
            $this->taskExec($command)->run();
        }
        return new Result(
            $this,
            0,
            'Drupal Console',
            ['time' => $this->getExecutionTime()]
        );

    }
}

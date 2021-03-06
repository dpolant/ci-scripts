<?php

namespace Mediacurrent\CiScripts\Task;

use Robo\Result;

class VagrantCheck extends \Mediacurrent\CiScripts\Task\Base
{

    use \JoeStewart\RoboDrupalVM\Task\loadTasks;
    use \JoeStewart\Robo\Task\Vagrant\loadTasks;
    use \Robo\Task\Base\loadTasks;

    protected $os_version = 'unknown';

    public function __construct() {
        $this->startTimer();
    }

    public function phpVersion() {
        if(PHP_VERSION_ID < 50509) {
            $this->say('Some dependencies require php version >= 5.5.9');
        }
        $this->taskExec('php --version')->run();


        return $this;
    }

    public function composerUpdate() {
        $this->taskExec('composer self-update')->run();

        return $this;
    }

    public function osVersion() {
        if(PHP_OS == 'Darwin') {
            $result = $this->taskExec('sw_vers')->run();
            $this->os_version = $result->getMessage();
        }

        return $this;
    }

    public function vagrantVersion() {
        $result = $this->taskVagrantVersion()->run();
        $vagrant_version = $result->getMessage();
        $this->taskExec('VBoxManage --version')->run();

        return $this;
    }

    public function boxUpdate() {
        $result = $this->taskVagrantBox()
          ->outdated()
          ->run();

        $vagrant_box_outdated = $result->getMessage();

        if(strpos($vagrant_box_outdated, 'A newer version of the box')) {
            $this->taskVagrantBox()
              ->update()
              ->run();
        }

        return $this;
    }

    public function pluginInstall() {
        $result = $this->taskVagrantPlugin()
          ->listPlugins()
          ->run();
        $value = $result->getMessage();

        if(!strpos($value, 'hostsupdater')) {
            $this->say('Recommended plugin Vagrant Hostsupdater not found.');
            $this->say('More information: https://github.com/cogitatio/vagrant-hostsupdater');
            if($this->confirm('Install vagrant-hostsupdater plugin now?')) {
                $this->taskVagrantPlugin()
                  ->install()
                  ->arg('vagrant-hostsupdater')
                  ->run();
            }
        }

        if(!strpos($value, 'vbguest')) {
            $this->say('Recommended plugin Vagrant VBGuest not found.');
            $this->say('More information: https://github.com/dotless-de/vagrant-vbguest');
            if($this->confirm('Install vagrant-vbguest plugin now?')) {
                $this->taskVagrantPlugin()
                  ->install()
                  ->arg('vagrant-vbguest')
                  ->run();
            }
        }

        return $this;
    }

    public function ansibleVersion() {
      // Check if Ansible is installed and check the version, if it is.
      $result = shell_exec('command -v ansible');
      if (!empty($result)) {
          $this->taskExec('ansible --version')->run();
      }
      else {
          $this->say('It appears that Ansible is not currently installed.');
      }

      return $this;
    }

    /**
     * @return Result
     */
    public function run()
    {

        $this->stopTimer();
        return new Result(
            $this,
            0,
            'ProjectInit',
            ['time' => $this->getExecutionTime()]
        );

    }
}

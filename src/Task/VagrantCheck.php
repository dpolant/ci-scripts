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

        if(strpos($vagrant_version, 'To upgrade to the latest version')) {
            if(!strpos($this->os_version, 'ProductVersion:	10.11')) {
                $this->say('Due to NFS sharing problems it is not advised to wait and upgrade to Vagrant >= 1.8.5');
            }
        }

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

        if(!strpos($value, 'vagrant-hostsupdater')) {
            $this->say('Recommended plugin Vagrant Hostsupdater not found.');
            $this->say('More information: https://github.com/cogitatio/vagrant-hostsupdater');
            if($this->confirm('Install vagrant-hostsupdater plugin now?')) {
                $this->taskVagrantPlugin()
                  ->install()
                  ->arg('vagrant-hostsupdater')
                  ->run();
            }
        }

        if(!strpos($value, 'cachier')) {
            $this->say('Recommended plugin Vagrant Cachier not found.');
            $this->say('More information: http://fgrehm.viewdocs.io/vagrant-cachier/');
            if($this->confirm('Install vagrant-cachier plugin now?')) {
                $this->taskVagrantPlugin()
                  ->install()
                  ->arg('vagrant-cachier')
                  ->run();
            }
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

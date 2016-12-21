<?php

namespace Mediacurrent\CiScripts\Command;

trait Vagrant
{

    use \JoeStewart\Robo\Task\Vagrant\loadTasks;


    /**
     * Vagrant check - Install plugins, update box, check version.
     *
     * @return object Result
     */
    public function vagrantCheck()
    {
        $this->taskVagrantCheck()
          ->phpVersion()
          ->composerUpdate()
          ->osVersion()
          ->vagrantVersion()
          ->ansibleVersion()
          ->boxUpdate()
          ->pluginInstall()
          ->run();
    }

}

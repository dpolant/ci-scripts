<?php

namespace Mediacurrent\CiScripts\Command;

trait Project
{

    use \JoeStewart\RoboDrupalVM\Task\loadTasks;

	/**
     * Project Init task.
     *
     * @return object Result
     */
    public function projectInit()
    {

        $this->taskVmInit()
            ->drupalvmPackage($this->drupalvm_package)
            ->configFile()
            ->vagrantFile()
            ->run();
        $this->taskProjectInit()
            ->drupalvmPackage($this->drupalvm_package)
            ->run();
    }

}

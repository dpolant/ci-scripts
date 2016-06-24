<?php

namespace Mediacurrent\CiScripts\Command;

trait Project
{

	/**
     * Project Init task.
     *
     * @return object Result
     */
    public function projectInit()
    {
        $this->taskProjectInit()
            ->vmInit($this->drupalvm_package)
            ->testsInit()
            ->run();
    }

}

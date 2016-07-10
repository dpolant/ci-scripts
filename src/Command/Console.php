<?php

namespace Mediacurrent\CiScripts\Command;

trait Console
{

    /**
     * Drupal Console command.
     *
     * @return object Result
     */
    public function console($console_command = null)
    {

        $this->taskConsole()
            ->consoleCommand($console_command)
            ->run();
    }

}

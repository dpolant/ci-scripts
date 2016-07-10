<?php

namespace Mediacurrent\CiScripts\Command;

trait Console
{

    /**
     * Drupal Console command.
     *
     *
     * @return object Result
     */
    public function console($console_command = null, $opts = null)
    {

        $this->taskConsole()
            ->consoleCommand($console_command)
            ->consoleOptions($opts)
            ->run();
    }

}

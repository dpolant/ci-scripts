<?php

namespace Mediacurrent\CiScripts\Task;


use Robo\Result;
use Robo\Common\ResourceExistenceChecker;
use Robo\Common\Timer;
use Robo\Common\TaskIO;

class SiteConfigImport extends \Mediacurrent\CiScripts\Task\Base
{
    use ResourceExistenceChecker;
    use \Mediacurrent\CiScripts\Task\loadTasks;

    /**
     * @return Result
     */
    public function run()
    {
        $this->startTimer();
        chdir($this->getWebRoot());
        $drush_alias =  '@' . $this->configuration['vagrant_hostname'];
        $isPrinted = isset($this->isPrinted) ? $this->isPrinted : false;
        // $this->isPrinted = false;
        // $result = $this->executeCommand('drush ' . $drush_alias . ' config-import -y');
        $result = $this->taskConsole()
            ->consoleCommand('config:import')
            ->run();
        $value = $result->getMessage();
        $this->isPrinted = $isPrinted;
        return new Result(
            $this,
            0,
            'SiteInstall',
            ['time' => $this->getExecutionTime()]
        );

    }
}

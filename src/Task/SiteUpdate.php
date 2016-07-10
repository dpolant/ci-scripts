<?php

namespace Mediacurrent\CiScripts\Task;


use Robo\Result;
use Robo\Common\ResourceExistenceChecker;
use Robo\Common\TaskIO;

class SiteUpdate extends \Mediacurrent\CiScripts\Task\Base
{
    use ResourceExistenceChecker;
    use \Robo\Task\Composer\loadTasks;
    use \Robo\Task\FileSystem\loadTasks;
    use \JoeStewart\RoboDrupalVM\Task\loadTasks;
    use \JoeStewart\Robo\Task\Vagrant\loadTasks;
    use \Mediacurrent\CiScripts\Task\loadTasks;

    public function composerInstall() {
        $this->taskComposerInstall()
            ->dir($this->getProjectRoot())
            ->run();
        return $this;
    }

    public function configImport() {
        $this->taskConsole()
            ->consoleCommand('config:import')
            ->run();
        return $this;
    }

    public function vagrantUp() {

        if(!$this->useVagrant()) {
            return $this;
        }

        if(!is_file($this->getProjectRoot() . 'Vagrantfile')) {
            $this->taskVmInit()
                ->vagrantFile('mediacurrent/mis_vagrant')
                ->run();
        }

        $result = $this->taskVagrantStatus()->printed(false)->run()->getMessage();
        if(!strpos($result, "The VM is running")) {
            $this->taskVagrantUp()->run();
        }
        return $this;
    }

    public function updateDb() {
        $this->taskConsole()
            ->consoleCommand('update:execute')
            ->arg('all')
            ->run();
        return $this;
    }

    /**
     * @return Result
     */
    public function run()
    {
        return new Result(
            $this,
            0,
            'SiteInstall',
            ['time' => $this->getExecutionTime()]
        );

    }
}

<?php

namespace Mediacurrent\CiScripts\Task;


use Robo\Result;
use Robo\Common\ResourceExistenceChecker;
use Robo\Common\TaskIO;

class SiteBuild extends \Mediacurrent\CiScripts\Task\Base
{
    use ResourceExistenceChecker;
    use \Robo\Task\Composer\loadTasks;
    use \Robo\Task\FileSystem\loadTasks;
    use \JoeStewart\RoboDrupalVM\Task\loadTasks;
    use \JoeStewart\Robo\Task\Vagrant\loadTasks;
    use \Boedah\Robo\Task\Drush\loadTasks;
    use \Mediacurrent\CiScripts\Task\loadTasks;

    public function composerInstall() {
        $this->taskComposerInstall()
            ->dir($this->getProjectRoot())
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

    public function siteInstall() {
        if(is_dir($this->getProjectRoot() .'/web/sites/' . $this->configuration['vagrant_hostname'])) {
            $this->taskFileSystemStack()
              ->chmod( $this->getProjectRoot() .'/web/sites/' . $this->configuration    ['vagrant_hostname'], 0755)
              ->chmod( $this->getProjectRoot() .'/web/sites/' . $this->configuration    ['vagrant_hostname'] . '/settings.php', 0644)
              ->run();
        }

        $this->taskSiteInstall()->run();

        if(is_dir($this->getProjectRoot() .'/web/sites/' . $this->configuration['vagrant_hostname'])) {
            $this->taskFileSystemStack()
              ->chmod( $this->getProjectRoot() .'/web/sites/' . $this->configuration    ['vagrant_hostname'], 0755)
              ->chmod( $this->getProjectRoot() .'/web/sites/' . $this->configuration    ['vagrant_hostname'] . '/settings.php', 0644)
              ->run();
        }

        $this->taskFileSystemStack()
          ->chmod( $this->getProjectRoot() .'/web/sites/' . $this->configuration['vagrant_hostname'] . '/files', 0777, 0000, true)
          ->run();
        
        return $this;
    }

    /**
     * @return Result
     */
    public function run()
    {
        $this->startTimer();

        $this->stopTimer();
        return new Result(
            $this,
            0,
            'SiteInstall',
            ['time' => $this->getExecutionTime()]
        );

    }
}

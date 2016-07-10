<?php

namespace Mediacurrent\CiScripts\Task;


use Robo\Result;
use Robo\Common\ResourceExistenceChecker;
use Robo\Common\TaskIO;

class Console extends \Mediacurrent\CiScripts\Task\Base
{
    use ResourceExistenceChecker;
    use \Robo\Task\Base\loadTasks;
    use \Robo\Task\Composer\loadTasks;
    use \Robo\Task\FileSystem\loadTasks;
    use \Robo\Task\Remote\loadTasks;
    use \JoeStewart\RoboDrupalVM\Task\loadTasks;
    use \JoeStewart\Robo\Task\Vagrant\loadTasks;
    use \Boedah\Robo\Task\Drush\loadTasks;
    use \Mediacurrent\CiScripts\Task\loadTasks;

    protected $arg;
    protected $console_command;
    protected $root;
    protected $uri;

    public function consoleCommand($console_command = null) {
        $this->console_command = $console_command;

        return $this;
    }

    public function arg($arg = null) {
        $this->arg = $arg;

        return $this;
    }
 
    /**
     * @return Result
     */
    public function run()
    {
        $console = $this->configuration['drupal_composer_install_dir'] . '/bin/drupal';
        $root = $this->configuration['drupal_composer_install_dir'] . '/web';
        $uri = 'htp://' . $this->configuration['vagrant_hostname'];
        $command = $console . ' --uri=' . $uri . ' --root=' . $root . ' ' . $this->console_command . ' ' . $this->arg;
        
        $this->printTaskInfo($command);
        if($this->useVagrant()) {
            $this->taskSshExec($this->configuration['vagrant_hostname'], 'vagrant')
                ->remoteDir($this->configuration['drupal_composer_install_dir'] . '/web/')
                ->exec($command)
                ->identityFile('~/.vagrant.d/insecure_private_key')
                ->run();
        }
        else {
            $this->taskExec($command)->run();
        }
        return new Result(
            $this,
            0,
            'Drupal Console',
            ['time' => $this->getExecutionTime()]
        );

    }
}

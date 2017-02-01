<?php

namespace Mediacurrent\CiScripts\Task;


use Robo\Result;
use Robo\Common\ResourceExistenceChecker;
use Robo\Common\TaskIO;

class SiteBuild extends \Mediacurrent\CiScripts\Task\Base
{
    use ResourceExistenceChecker;
    use \Robo\Task\Composer\loadTasks;
    use \Robo\Task\File\loadTasks;
    use \Robo\Task\FileSystem\loadTasks;
    use \JoeStewart\RoboDrupalVM\Task\loadTasks;
    use \JoeStewart\Robo\Task\Vagrant\loadTasks;
    use \Boedah\Robo\Task\Drush\loadTasks;
    use \Mediacurrent\CiScripts\Task\loadTasks;

    public function composerInstall() {
        $this->taskComposerInstall()
            ->dir($this->getProjectRoot())
            ->run();
        if(!file_exists($this->getProjectRoot() . '/vendor/bin')) {
            $this->taskFilesystemStack()
                ->symlink('../bin', '../vendor/bin')
                ->run();
        }
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

        $webroot = (isset($this->configuration['drupal_webroot'])) ? $this->configuration['drupal_webroot'] : 'web';
        $site_directory = $this->getProjectRoot() .'/' . $webroot . '/sites/' . $this->configuration['vagrant_hostname'];

        if(is_dir($site_directory)) {
            $this->taskFileSystemStack()
                ->chmod($site_directory, 0755)
                ->chmod($site_directory . '/settings.php', 0644)
                ->run();
        } else {
            $this->taskFileSystemStack()
                ->mkdir($site_directory)
                ->run();

            $this->taskConcat([
                    $this->getProjectRoot() . '/' . $webroot . '/sites/default/default.settings.php',
                    $this->getProjectRoot() . '/' . $webroot . '/sites/example.settings.local.php',
                ])
                ->to($site_directory . '/settings.php')
                ->run();

            $text = <<<EOF
if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}


EOF;
            $this->taskWriteToFile($site_directory . '/settings.php')
                ->append(true)
                ->text($text)
                ->replace("\n<?php\n", '')
                ->run();

            $this->taskWriteToFile($site_directory . '/services.yml')
                ->textFromFile($this->getProjectRoot() . '/' . $webroot . '/sites/default/default.services.yml')
                ->replace("debug: false", 'debug: true')
                ->run();
        }

        $this->taskSiteInstall()->run();

        if(is_dir($site_directory)) {
            $this->taskFileSystemStack()
                ->chmod($site_directory, 0755)
                ->chmod($site_directory . '/settings.php', 0644)
                ->run();
        }

        if(is_dir($site_directory . '/files')) {
            $this->taskFileSystemStack()
                ->chmod($site_directory . '/files', 0777, 0000, true)
                ->run();
        }
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

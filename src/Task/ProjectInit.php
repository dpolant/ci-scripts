<?php

namespace Mediacurrent\CiScripts\Task;

use Robo\Common\ExecOneCommand;
use Robo\Result;
use Robo\Common\ResourceExistenceChecker;
use Robo\Common\Timer;
use Robo\Common\TaskIO;

class ProjectInit extends \Mediacurrent\CiScripts\Task\Base
{
    use ResourceExistenceChecker;
    use \JoeStewart\RoboDrupalVM\Task\loadTasks;
    use \Robo\Task\Base\loadTasks;
    use \Robo\Task\File\loadTasks;
    use \Robo\Task\FileSystem\loadTasks;
    use \Robo\Task\Remote\loadTasks;

    public function drushAlias() {
        $drushalias_filename = $this->configuration['vagrant_hostname'] . '.aliases.drushrc.php';
        $drushalias_source = $this->getVendorDir() . '/mediacurrent/ci-scripts/files/example.mcdev.aliases.drushrc.php';
        $drushalias_dest = $this->getProjectRoot() . '/drush/' . $drushalias_filename;
        if (!is_file($drushalias_dest)) {
            $this->taskFileSystemStack()
              ->copy($drushalias_source, $drushalias_dest)
              ->run();
            $this->taskReplaceInFile($drushalias_dest)
              ->from('example.mcdev')
              ->to($this->configuration['vagrant_hostname'])
              ->run();

        }

        return $this;
    }
    
    public function readme() {

        $readme_file = $this->getProjectRoot() . '/README.md';
        $readme_template = $this->getVendorDir() . '/mediacurrent/ci-scripts/files/README.md';

        $result = $this->taskExec('git ls-remote --get-url')->run();
        $git_remote_url = str_replace("\n", '', $result->getMessage());

        $bitbucket_remote = explode('/', $git_remote_url);
        $bitbucket_project = $bitbucket_remote[1];

        if(!is_file($readme_file) || !preg_grep("#$git_remote_url#", file($readme_file))) {
            $this->taskWriteToFile($readme_file)
                ->textFromFile($readme_template)
                ->replace('{{ git_remote_url }}', $git_remote_url)
                ->replace('{{ vagrant_hostname }}', $this->configuration['vagrant_hostname'])
                ->replace('{{ vagrant_ip }}', $this->configuration['vagrant_ip'])
                ->replace('{{ bitbucket_project }}', $bitbucket_project)
                ->run();
        }

        return $this;
    }

    public function testsInit($vagrant_hostname = null) {
        $this->taskRsync()
            ->fromPath($this->getVendorDir() . '/mediacurrent/ci-tests/tests')
            ->toPath($this->getProjectRoot())
            ->archive()
            ->verbose()
            ->exclude('.gitignore')
            ->option('--ignore-existing')
            ->recursive()
            ->run();

        if(!is_file($this->getProjectRoot() . '/tests/behat/behat.local.yml')) {
            $this->taskFileSystemStack()
                ->copy($this->getProjectRoot() . '/tests/behat/behat.local.yml.example', $this->getProjectRoot() . '/tests/behat/behat.local.yml')
                ->run();
                $this->taskReplaceInFile($this->getProjectRoot() . '/tests/behat/behat.local.yml')
                ->from('base_url:')
                ->to('base_url: http://' . $vagrant_hostname)
                ->run();

        }
        return $this;
    }

    public function vagrantConfig($vagrant_hostname = null, $vagrant_ip = null) {
        if($vagrant_hostname) {
        $this->taskReplaceInFile($this->getVagrantConfig())
            ->from('example.mcdev')
            ->to($vagrant_hostname)
            ->run();
        $this->taskReplaceInFile($this->getVagrantConfig())
            ->from('example_mcdev')
            ->to(str_replace('.', '_', $vagrant_hostname))
            ->run();
        }
        if($vagrant_ip) {
        $this->taskReplaceInFile($this->getVagrantConfig())
            ->from('192.168.50.4')
            ->to($vagrant_ip)
            ->run();
        }
        return $this;
    }

    public function vmInit($drupalvm_package) {
        $this->taskVmInit()
          ->drupalvmPackage($drupalvm_package)
          ->configFile()
          ->vagrantFile($drupalvm_package)
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
            'ProjectInit',
            ['time' => $this->getExecutionTime()]
        );

    }
}

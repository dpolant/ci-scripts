<?php

namespace Mediacurrent\CiScripts\Task;


use Robo\Result;


class Theme extends \Mediacurrent\CiScripts\Task\Base
{
    use \Robo\Task\Base\loadTasks;
    use \Robo\Task\Npm\loadTasks;
    use \Mediacurrent\CiScripts\Task\loadTasks;

    protected $exitCode = 0;
    protected $pathToTheme;

    public function __construct()
    {
        $this->startTimer();
        parent::__construct();
    }

    public function nvmUse()
    {

        $command = 'nvm use';
        $this->taskExec($command)
            ->dir($this->pathToTheme)
            ->run();
        return $this;
    }

    public function npmInstall()
    {

        $this->taskNpmInstall()
            ->dir($this->pathToTheme)
            ->run();
        return $this;
    }

    public function npmRunStyleGuide()
    {

        $command = 'npm run styleguide';
        $this->taskExec($command)
            ->dir($this->pathToTheme)
            ->run();
        return $this;
    }

    public function themeDirectory($pathToTheme = null)
    {
        if(!$pathToTheme) {
            $drush = 'drush --uri=' . $this->configuration['vagrant_hostname'] . ' --root=' . $this->getWebRoot() . ' ';
            $drush_command = 'php-eval "return \Drupal::theme()->getActiveTheme()->getPath();"';
            $command = $drush . $drush_command;
            $result = shell_exec($command);
            $active_theme = str_replace(array( "'", "\n"), '', $result);
            $this->pathToTheme = $this->getWebRoot() . '/' . $active_theme;

        } else {
            $this->pathToTheme = $pathToTheme;
        }

        return $this;
    }

    /**
     * @return Result
     */
    public function run()
    {
        $this->stopTimer();
        return new Result(
            $this,
            $this->exitCode,
            'Theme',
            ['time' => $this->getExecutionTime()]
        );

    }
}

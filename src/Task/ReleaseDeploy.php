<?php

namespace Mediacurrent\CiScripts\Task;

use Robo\Result;

class ReleaseDeploy extends \Mediacurrent\CiScripts\Task\Base
{

    use \Robo\Task\Vcs\loadTasks;

    private $release_repo_dest;

    public function __construct()
    {
        $this->startTimer();

        parent::__construct();
    }

   public function releaseDeploy($deploy_host = null)
    {
        if(!$deploy_host && !empty($this->configuration['deploy_host'])) {
            $deploy_host = $this->configuration['deploy_host'];
        }

        switch (strtolower($deploy_host)) {
            case 'acquia':
            case 'git':
                $this->release_repo_dest = $this->getProjectRoot() . '/' . $this->configuration['build_directory'] . '/release_repo';
                $this->releaseDeployGit();
                break;

            default:
                break;
        }

        return $this;
    }

    public function releaseDeployGit()
    {

        if(exec('ls -1 ' . $this->release_repo_dest . '/.git')) {
            $this->taskGitStack()
                ->dir($this->release_repo_dest)
                ->push( 'origin', $this->configuration['build_branch'])
                ->run();
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
            0,
            'ReleaseDeploy',
            ['time' => $this->getExecutionTime()]
        );
    }
}

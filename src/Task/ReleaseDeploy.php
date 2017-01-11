<?php

namespace Mediacurrent\CiScripts\Task;

use Robo\Result;

class ReleaseDeploy extends \Mediacurrent\CiScripts\Task\Base
{

    use \Robo\Task\Remote\loadTasks;
    use \Robo\Task\Vcs\loadTasks;

    private $build_path;
    private $release_repo_dest;

    public function __construct()
    {
        $this->startTimer();

        parent::__construct();

        if(empty($this->configuration['build_directory'])) {
            $this->configuration['build_directory'] = 'build';
        }
        $this->build_path = $this->getProjectRoot() . '/' . $this->configuration['build_directory'];
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

    public function releaseDeployGit($build_branch = null, $release_tag = null)
    {

        $this->release_repo_dest = $this->build_path . '/release_repo';

        if(!$build_branch) {
            $build_branch = $this->configuration['build_branch'];
        }

        if(exec('ls -1 ' . $this->release_repo_dest . '/.git')) {
            $this->taskGitStack()
                ->dir($this->release_repo_dest)
                ->push( 'origin', $build_branch)
                ->run();
        }

        if($release_tag) {
            $this->taskGitStack()
                ->dir($this->release_repo_dest)
                ->push( 'origin', $release_tag)
                ->run();
        }

        return $this;
    }

    public function releaseDeployRsync($deploy_env = null, $release_tag = null)
    {

        if(!empty($this->configuration[$deploy_env . '_release_host'])) {
            $release_deploy_host = $this->configuration[$deploy_env . '_release_host'];
        }
        else {
            $this->say('No matching release reploy host found');
            return $this;
        }

        $this->release_repo_dest = $this->build_path . '/release_repo';

        $drupal_webroot = (isset($this->configuration['drupal_webroot'])) ? $this->configuration['drupal_webroot'] : 'web';

        $this->taskRsync()
                ->fromPath($this->release_repo_dest . '/')
                ->toHost($release_deploy_host)
                ->toUser($this->configuration['release_host_user'])
                ->toPath($this->configuration['release_deploy_dest'] . '/')
                ->recursive()
                ->option('links')
                ->exclude($drupal_webroot . '/sites/default/files')
                ->exclude($drupal_webroot . '/sites/default/settings.local.php')
                ->delete()
                ->verbose()
                ->compress()
                ->run();

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

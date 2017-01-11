<?php

namespace Mediacurrent\CiScripts\Task;

use Robo\Result;

class ReleaseBuild extends \Mediacurrent\CiScripts\Task\Base
{

    use \Robo\Task\Base\loadTasks;
    use \Robo\Task\Composer\loadTasks;
    use \Robo\Task\File\loadTasks;
    use \Robo\Task\FileSystem\loadTasks;
    use \Robo\Task\Remote\loadTasks;
    use \Robo\Task\Vcs\loadTasks;

    private $build_path;
    private $commit_msg;
    private $project_docroot;
    private $project_drupal_root;
    private $project_repo_dest;
    private $release_docroot;
    private $release_drupal_root;
    private $release_repo_dest;

    public function __construct()
    {
        $this->startTimer();

        parent::__construct();

        if(empty($this->configuration['build_branch'])) {
            $this->configuration['build_branch'] = 'develop';
        }
        if(empty($this->configuration['build_directory'])) {
            $this->configuration['build_directory'] = 'build';
        }
        $this->build_path = $this->getProjectRoot() . '/' . $this->configuration['build_directory'];
        $this->project_repo_dest = $this->build_path . '/project_repo';
        $this->release_repo_dest = $this->build_path . '/release_repo';
        $this->project_drupal_root = 'web';
        if(!empty($this->configuration['project_drupal_root'])) {
             $this->project_drupal_root = $this->configuration['project_drupal_root'];
        }
        $this->project_docroot = $this->project_repo_dest . '/' . $this->project_drupal_root;
        $this->release_drupal_root = 'web';
        if(!empty($this->configuration['release_drupal_root'])) {
             $this->release_drupal_root = $this->configuration['release_drupal_root'];
        }
        $this->release_docroot = $this->release_repo_dest . '/' . $this->release_drupal_root;
    }

    public function releaseHost($deploy_host = null)
    {

        if(!$deploy_host && !empty($this->configuration['deploy_host'])) {
            $deploy_host = $this->configuration['deploy_host'];
        }

        if(!$this->configuration['project_repo']
            || !$this->configuration['release_repo']
            || !$deploy_host) {

            return $this;
        }

        switch (strtolower($deploy_host)) {
            case 'acquia':
                $this->release_drupal_root = 'docroot';
                $this->release_docroot = $this->release_repo_dest . '/' . $this->release_drupal_root;
                $this->releaseBuildDirectories()
                    ->releaseGitCheckout()
                    ->releaseSyncProject()
                    ->releaseSyncDocroot()
                    ->releaseSetDocroot()
                    ->releaseComposerInstall()
                    ->releaseCleanupModuleVcs()
                    ->releaseCommit();
                break;

            default:
                break;
        }

        return $this;
    }

    public function releaseBuildDirectories()
    {

        $this->taskFilesystemStack()
           ->mkdir($this->build_path)
           ->run();

        $this->taskFilesystemStack()
           ->mkdir($this->project_repo_dest)
           ->run();

        $this->taskFilesystemStack()
           ->mkdir($this->release_repo_dest)
           ->run();

        return $this;
    }

    public function releaseGitCheckout($build_branch = null, $release_tag = null)
    {
        $this->releaseGitCheckoutProject($build_branch, $release_tag);
        $this->releaseGitCheckoutRelease($build_branch);

        return $this;
    }

    public function releaseGitCheckoutProject($build_branch = null, $release_tag = null)
    {

        if(!$build_branch) {
            $build_branch = $this->configuration['build_branch'];
        }

        if(exec('ls -1 ' . $this->project_repo_dest . '/.git')) {
            $this->taskGitStack()
                ->dir($this->project_repo_dest)
                ->pull( 'origin', $build_branch)
                ->checkout($build_branch)
                ->run();
        }
        else {
            $this->taskGitStack()
                ->cloneRepo($this->configuration['project_repo'], $this->project_repo_dest)
                ->checkout($build_branch)
                ->run();
        }

        if($release_tag) {
            $result = $this->taskGitStack()
                ->dir($this->project_repo_dest)
                ->checkout($release_tag)
                ->run();
            if(!$result->wasSuccessful()) {
                exit(1);
            }
        }

        $gitlog_cmd = 'cd ' . $this->project_repo_dest . ' && git log --format=%B -n 1';
        $this->commit_msg = shell_exec( $gitlog_cmd);
        $this->printTaskInfo("\ncommit message = " . $this->commit_msg);

        return $this;
    }

    public function releaseGitCheckoutRelease($build_branch = null, $release_tag = null)
    {

        if(!$build_branch) {
            $build_branch = $this->configuration['build_branch'];
        }

        if(exec('ls -1 ' . $this->release_repo_dest . '/.git')) {
            chdir($this->release_repo_dest);

            $local_branch = exec('git branch | grep ' . $build_branch);
            $remote_branch = exec('git branch -a | grep origin/' . $build_branch);
            if($local_branch || $remote_branch) {
                $this->taskGitStack()
                    ->dir($this->release_repo_dest)
                    ->checkout($build_branch)
                    ->run();
                if($remote_branch) {
                    $this->taskGitStack()
                        ->dir($this->release_repo_dest)
                        ->pull( 'origin', $build_branch)
                        ->run();
                }
            }
            else {
                $this->taskExec( 'git checkout -b ' . $build_branch)
                    ->dir($this->release_repo_dest)
                    ->run();
            }
        }
        else {
            $this->taskGitStack()
                ->cloneRepo($this->configuration['release_repo'], $this->release_repo_dest)
                ->run();

            chdir($this->release_repo_dest);
            if(exec('git branch -a | grep origin/' . $build_branch)) {
                $this->taskGitStack()
                    ->dir($this->release_repo_dest)
                    ->checkout($build_branch)
                    ->run();
            }
            else {
                $this->taskExec( 'git checkout -b ' . $build_branch)
                    ->dir($this->release_repo_dest)
                    ->run();
            }

            if($release_tag) {
                $result = $this->taskGitStack()
                    ->dir($this->project_repo_dest)
                    ->checkout($release_tag)
                    ->run();
                if(!$result->wasSuccessful()) {
                    exit(1);
                }
            }
        }

        return $this;
    }

    public function releaseSetDocroot()
    {
        if($this->project_drupal_root !== $this->release_drupal_root) {
            $this->taskReplaceInFile($this->release_repo_dest . '/composer.json')
                ->from($this->project_drupal_root . '/')
                ->to($this->release_drupal_root . '/')
                ->run();

            $this->taskReplaceInFile($this->release_repo_dest . '/scripts/composer/    ScriptHandler.php')
                ->from('/' . $this->project_drupal_root)
                ->to('/' . $this->release_drupal_root)
                ->run();

            return $this;
        }
    }

    public function releaseSyncProject()
    {
        $this->taskRsync()
            ->fromPath($this->project_repo_dest . '/')
            ->toPath($this->release_repo_dest . '/')
            ->recursive()
            ->exclude('composer.lock')
            ->exclude('vendor')
            ->exclude('bin')
            ->exclude($this->project_drupal_root)
            ->exclude('tests')
            ->exclude('.git')
            ->exclude('.gitignore')
            ->option('links')
            ->run();

        return $this;
    }

    public function releaseSyncDocroot()
    {
        $this->taskRsync()
            ->fromPath($this->project_docroot . '/')
            ->toPath($this->release_docroot . '/')
            ->recursive()
            ->delete()
            ->exclude('.git')
            ->run();

        return $this;
    }

    public function releaseComposerInstall() {
        $composer_cmd = 'composer install --no-ansi --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader';

        $this->taskExec($composer_cmd)
            ->dir($this->release_repo_dest)
            ->run();

        return $this;
    }

    public function releaseCleanupModuleVcs() {
        $shell_cmd = 'find . -type d | grep .git | xargs rm -rf';

        $this->taskExec($shell_cmd)
            ->dir($this->release_docroot)
            ->run();

        $this->taskExec($shell_cmd)
            ->dir($this->release_repo_dest . '/vendor')
            ->run();

        return $this;
    }

    public function releaseCommit($release_tag = null)
    {
        $dir = $this->release_repo_dest;
        $git_status = shell_exec( 'cd ' . $dir . ' && git status');

        $this->printTaskInfo('git status = ' . $git_status);

        if(!strpos($git_status, 'nothing to commit, working directory clean')) {
            $this->taskGitStack()
                ->dir($dir)
                ->add('-Af')
                ->run();

            $commit_msg = $this->configuration['build_branch'];
            if($release_tag) {
                $commit_msg .= ' ' . $release_tag;
            }
            $commit_msg .= ' build at ' . date('c') . "\n";
            $commit_msg .= $this->commit_msg;

            $this->taskGitStack()
                ->dir($dir)
                ->commit($commit_msg)
                ->run();

            if($release_tag) {
                $this->taskGitStack()
                ->dir($dir)
                ->tag($release_tag)
                ->run();
            }

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
            'ReleaseBuild',
            ['time' => $this->getExecutionTime()]
        );
    }
}

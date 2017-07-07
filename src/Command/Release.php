<?php

namespace Mediacurrent\CiScripts\Command;

trait Release
{

    /**
     * Release Build command.
     *
     * release:build runs the following -
     *
     *  create build directory
     *  checkout project repository
     *  checkout release repository
     *  sync files from poject to release as needed
     *  modify docroot directory as needed
     *  composer install
     *  commit changes to release repository
     *
     * deploy_host: Acquia
     *
     * Requires the following variables be set
     * for the project in config/config.yml:
     *
     * Acquia -
     *
     * project_repo: git@bitbucket.org:mediacurrent/mis_example.git
     * project_drupal_root: web
     * release_repo: development10@svn.devcloud.hosting.acquia.com:development.git
     * release_drupal_root: docroot
     * deploy_host: Acquia
     *
     * Pantheon -
     *
     * project_repo: git@bitbucket.org:mediacurrent/mis_example.git
     * release_repo: ssh://codeserver.dev.xxx@codeserver.dev.xxx.drush.in:2222/~/repository.git
     * deploy_host: Pantheon
     *
     * Hosts not deploying via git -
     *
     * release_repo: git@bitbucket.org:mediacurrent/drupal-project.git
     * deploy_host: generic or Blackmesh, etc
     *
     * @param string $deploy_host Host for Deployment ( Acquia, Pantheon)
     * @param string $build_branch Branch to build
     * @param string $release_tag Optional label for release
     *
     * @return object Result
     */
    public function releaseBuild($deploy_host = null, $build_branch = 'master', $release_tag = null)
    {

        if(!$deploy_host && !empty($this->configuration['deploy_host'])) {
            $deploy_host = $this->configuration['deploy_host'];
        }

        if(empty($this->configuration['release_repo'])
            || !$deploy_host) {

            $this->say('Configuration variables missing.  Consult help output.');
            return $this;
        }

        switch (strtolower($deploy_host)) {
            case 'acquia':
            case 'git':
            case 'pantheon':
                $this->taskReleaseBuild()
                    ->releaseBuildDirectories()
                    ->releaseGitCheckout($build_branch, $release_tag)
                    ->releaseSyncProject()
                    ->releaseSyncDocroot()
                    ->releaseSetDocroot()
                    ->releaseComposerInstall()
                    ->releaseCleanupModuleVcs()
                    ->releaseCommit($release_tag);
                break;

            default:
                $this->taskReleaseBuild()
                    ->releaseBuildDirectories()
                    ->releaseGitCheckoutRelease($build_branch, $release_tag)
                    ->releaseComposerInstall();
                break;
        }

        $this->taskReleaseBuild()
            ->run();
    }

    /**
     * Release Deploy Command.
     *
     * release deploy runs the following -
     *
     *  pushes deploy release to remote
     *
     * Requires the following variables be set
     * for the project in config/config.yml:
     *
     * Acquia ( git, Pantheon)-
     *
     * project_repo: git@bitbucket.org:mediacurrent/mis_example.git
     * release_repo: development10@svn.devcloud.hosting.acquia.com:development.git
     * deploy_host: Acquia
     *
     * Rsync -
     *
     * release_repo: git@bitbucket.org:mediacurrent/drupal-project.git
     * release_host_user: username
     * dev_release_host: server.example.com
     * release_deploy_dest: "/var/www/htdocs"
     *
     * @param string $deploy_host Host for Deployment ( Acquia, Pantheon, rsync)
     * @param string $build_branch Branch or environment to deploy
     * @param string $release_tag Optional label for release
     * @param array $opts
     *
     * @option $yes Deploy immediately without confirmation
     *
     * @return object Result
     */
    public function releaseDeploy($deploy_host = null, $build_branch = 'master', $release_tag = null, $opts = ['yes|y' => false])
    {
        if ( $opts['yes'] || $this->confirm("Deploy release now. Are you sure?")) {

            if(!$deploy_host && !empty($this->configuration['deploy_host'])) {
                $deploy_host = $this->configuration['deploy_host'];
            }

            switch (strtolower($deploy_host)) {
                case 'acquia':
                case 'git':
                case 'pantheon':
                    $this->taskReleaseDeploy()
                        ->releaseDeployGit($build_branch, $release_tag)
                        ->run();
                    break;

                case 'blackmesh':
                case 'rsync':
                    $this->taskReleaseDeploy()
                        ->releaseDeployRsync($build_branch, $release_tag)
                        ->run();
                    break;

                default:
                    break;
            }
        }
    }
}

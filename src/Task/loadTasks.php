<?php
namespace Mediacurrent\CiScripts\Task;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{

    /**
     * @return Console
     */
    protected function taskConsole()
    {
        return new Console();
    }

    /**
     * @return Drush
     */
    protected function taskDrush()
    {
        return new Drush();
    }

    /**
     * @return ProjectInit
     */
    protected function taskProjectInit()
    {
        return new ProjectInit();
    }

    /**
     * @return ReleaseBuild
     */
    protected function taskReleaseBuild()
    {
        return new ReleaseBuild();
    }

    /**
     * @return ReleaseDeploy
     */
    protected function taskReleaseDeploy()
    {
        return new ReleaseDeploy();
    }

    /**
     * @return SiteBuild
     */
    protected function taskSiteBuild()
    {
        return new SiteBuild();
    }

    /**
     * @return SiteInstall
     */
    protected function taskSiteInstall()
    {
        return new SiteInstall();
    }

    /**
     * @return SiteUpdate
     */
    protected function taskSiteUpdate()
    {
        return new SiteUpdate();
    }

    /**
     * @return VagrantCheck
     */
    protected function taskVagrantCheck()
    {
        return new VagrantCheck();
    }
}

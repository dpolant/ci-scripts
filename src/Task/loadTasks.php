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
     * @return DatabaseImport
     */
    protected function taskDatabaseImport()
    {
        return new DatabaseImport();
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
     * @return SiteTest
     */
    protected function taskSiteTest()
    {
        return new SiteTest();
    }

    /**
     * @return SiteUpdate
     */
    protected function taskSiteUpdate()
    {
        return new SiteUpdate();
    }

    /**
     * @return Theme
     */
    protected function taskThemeBuild()
    {
        return new Theme();
    }

   /**
     * @return Theme
     */
    protected function taskThemeCompile()
    {
        return new Theme();
    }

    /**
     * @return Theme
     */
    protected function taskThemeStyleGuide()
    {
        return new Theme();
    }

    /**
     * @return Theme
     */
    protected function taskThemeWatch()
    {
        return new Theme();
    }

    /**
     * @return VagrantCheck
     */
    protected function taskVagrantCheck()
    {
        return new VagrantCheck();
    }
}

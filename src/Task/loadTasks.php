<?php
namespace Mediacurrent\CiScripts\Task;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{

   /**
     * Return services.
     */
    public static function getProjectServices()
    {
        return new SimpleServiceProvider(
            [
                'taskConsole' => Console::class,
                'taskProjectInit' => ProjectInit::class,
                'taskSiteBuild' => SiteBuild::class,
                'taskSiteInstall' => SiteInstall::class,
                'taskSiteUpdate' => SiteUpdate::class,
            ]
        );
    }

    /**
     * @return Console
     */
    protected function taskConsole()
    {
        return new Console();
    }

    /**
     * @return ProjectInit
     */
    protected function taskProjectInit()
    {
        return new ProjectInit();
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
}

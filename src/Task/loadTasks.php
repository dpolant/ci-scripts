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
                'taskProjectInit' => ProjectInit::class,
                'taskSiteBuild' => SiteBuild::class,
                'taskSiteInstall' => SiteInstall::class,
            ]
        );
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

}

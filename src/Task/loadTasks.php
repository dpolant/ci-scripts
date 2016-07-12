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
                'taskSiteInstall' => SiteConfigImport::class,
                'taskSiteInstall' => SiteInstall::class,
                'taskSiteUpdate' => SiteUpdate::class,
                'taskVagrantCheck' => VagrantCheck::class,
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
     * @return SiteConfigImport
     */
    protected function taskSiteConfigImport()
    {
        return new SiteConfigImport();
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

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

}

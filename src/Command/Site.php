<?php

namespace Mediacurrent\CiScripts\Command;

trait Site
{

    /**
     * Site Build task.
     *
     * @return object Result
     */
    public function siteBuild()
    {

        $this->taskSiteBuild()
            ->composerInstall()
            ->vagrantUp()
            ->run();
    }

    /**
     * Site Install task.
     *
     * @return object Result
     */
    public function siteInstall()
    {
        $this->taskSiteInstall()->run();
    }

}

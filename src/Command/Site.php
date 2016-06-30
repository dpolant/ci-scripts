<?php

namespace Mediacurrent\CiScripts\Command;

trait Site
{

    /**
     * Site Build task.
     *
     * site:build runs the following -
     *
     *  composer install
     *  vagrant up if required
     *  Ensures sites/example.mcdev and settings.php are writable
     *  drush site-install
     *  Ensures sites/example.mcdev/files is writable
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
     * site:install runs drush site-install with configuration from config/config.yml
     *
     * @return object Result
     */
    public function siteInstall()
    {
        $this->taskSiteInstall()->run();
    }

}

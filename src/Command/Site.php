<?php

namespace Mediacurrent\CiScripts\Command;

trait Site
{

    /**
     * Site Build command.
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
            ->siteInstall()
            ->run();
    }

    /**
     * Site Install command.
     *
     * site:install runs drush site-install with configuration from config/config.yml
     *
     * @return object Result
     */
    public function siteInstall()
    {
        $this->taskSiteInstall()->run();
    }

    /**
     * Site Update command.
     *
     * site:update runs the following -
     *
     *  composer install
     *  vagrant up if required
     *  drush config-import
     *  drush updatedb
     *
     * @return object Result
     */
    public function siteUpdate()
    {
        $this->taskSiteUpdate()

          ->composerInstall()
          ->vagrantUp()
          ->configImport()
          ->updateDB()
          ->run();
    }
}

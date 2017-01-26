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
     * Site Test command.
     *
     * site:test runs the requested tests on the site
     *
     * @param array $opts
     * @option $behat Run behat tests.
     * @option $pa11y Run pa11y accessibility tests.
     * @option $phpunit Run phpunit tests.
     * @option $phpcs Run Drupal coding standards via code sniiffer.
     *
     * @return object Result
     */
    public function siteTest($test_argument = null, $opts = [ 'behat' => false, 'pa11y' => false, 'phpunit' => false, 'phpcs' => false])
    {
        $this->taskSiteTest()
          ->testArgument($test_argument)
          ->testOptions($opts)
          ->run();
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
          ->updateDB()
          ->configImport()
          ->run();
    }
}

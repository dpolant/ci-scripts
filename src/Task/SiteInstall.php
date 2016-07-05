<?php

namespace Mediacurrent\CiScripts\Task;


use Robo\Result;
use Robo\Common\ResourceExistenceChecker;
use Robo\Common\Timer;
use Robo\Common\TaskIO;

class SiteInstall extends \Mediacurrent\CiScripts\Task\Base
{
    use ResourceExistenceChecker;
    use \Boedah\Robo\Task\Drush\loadTasks;

    /**
     * @return Result
     */
    public function run()
    {
        $this->startTimer();
        chdir($this->getWebRoot());
        $dbconnection_string = $this->configuration['drupal_mysql_user'] .':' . $this->configuration['drupal_mysql_password'] . '@localhost/' . $this->configuration['vagrant_machine_name'];
        $this->taskDrushStack()
            ->siteAlias('@' . $this->configuration['vagrant_hostname'])
            ->sitesSubdir($this->configuration['vagrant_hostname'])
            ->mysqlDbUrl($dbconnection_string)
            ->siteName($this->configuration['drupal_site_name'])
            ->siteMail('admin@example.com')
            ->accountMail('admin@example.com')
            ->accountName($this->configuration['drupal_account_name'])
            ->accountPass($this->configuration['drupal_account_pass'])
            ->siteInstall($this->configuration['drupal_install_profile'])
            ->run();
        $this->stopTimer();
        return new Result(
            $this,
            0,
            'SiteInstall',
            ['time' => $this->getExecutionTime()]
        );

    }
}

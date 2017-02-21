<?php

use PHPUnit\Framework\TestCase;

class DrushTest extends TestCase
{
    use \Mediacurrent\CiScripts\Task\loadTasks;

    public function testDrushTask()
    {
        $drush_command = 'help';
        $opts = 'status';

        $command = $this->taskDrush()
            ->drushCommand($drush_command)
            ->drushOptions($opts)
            ->getCommand('/home/vagrant/docroot', 'http://example.mcdev');
        $expected = '/home/vagrant/docroot/bin/drush --uri=http://example.mcdev --root=/home/vagrant/docroot/web help status';
        $this->assertEquals($expected, $command);
    }

}

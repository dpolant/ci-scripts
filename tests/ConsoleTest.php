<?php

class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    use \Mediacurrent\CiScripts\Task\loadTasks;

    public function testConsoleTask()
    {
        $console_command = 'list';
        $opts = 'test';

        $command = $this->taskConsole()
            ->consoleCommand($console_command)
            ->consoleOptions($opts)
            ->getCommand('/home/vagrant/docroot', 'http://example.mcdev');
        $expected = '/home/vagrant/docroot/bin/drupal --uri=http://example.mcdev --root=/home/vagrant/docroot/web list test';
        $this->assertEquals($expected, $command);
    }

}
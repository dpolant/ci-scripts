<?php

namespace Mediacurrent\CiScripts\Task;


use Robo\Result;
use Robo\Common\ResourceExistenceChecker;
use Robo\Common\Timer;
use Robo\Common\TaskIO;

class ProjectInit extends \Mediacurrent\CiScripts\Task\Base
{
    use Timer;
    use ResourceExistenceChecker;

    /**
     * @return Result
     */
    public function run()
    {
        $this->startTimer();
        $this->stopTimer();
        return new Result(
            $this,
            0,
            'ProjectInit',
            ['time' => $this->getExecutionTime()]
        );

    }
}

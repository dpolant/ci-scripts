<?php

namespace Mediacurrent\CiScripts\Task;

use Robo\Result;
use Robo\Task\BaseTask;
use Robo\Common\ExecOneCommand;
use Robo\Contract\CommandInterface;
use Robo\Contract\TaskInterface;
use Robo\Contract\PrintedInterface;
use Robo\Exception\TaskException;
use Robo\Common\Timer;

class Project extends \Mediacurrent\CiScripts\Task\Base
{
    use Timer;
 
    /**
     * @return Result
     */
    public function run()
    {
        return new Result(
            $this,
            0,
            'Vm'
        );
    }
}

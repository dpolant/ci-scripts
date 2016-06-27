<?php

namespace Mediacurrent\CiScripts\Command;

trait Project
{

    use \JoeStewart\RoboDrupalVM\Task\loadTasks;

	/**
     * Project Init task.
     *
     * @return object Result
     */
    public function projectInit()
    {

        $this->taskVmInit()
            ->drupalvmPackage($this->drupalvm_package)
            ->configFile()
            ->vagrantFile()
            ->run();
        $this->taskProjectInit()
            ->drupalvmPackage($this->drupalvm_package)
            ->run();
    }

    /**
     * Project task - Create Drush Alias.
     *
     * @return object Result
     */
    public function projectCreateDrushAlias()
    {

        $drushalias_filename = $this->configuration['vagrant_hostname'] . '.aliases.drushrc.php';
        $drushalias_source = $this->vm->getVendorDir() . '/mediacurrent/ci-scripts/files/example.mcdev.aliases.drushrc.php';
        $drushalias_dest = $this->vm->getProjectRoot() . '/drush/' . $drushalias_filename;
        if (!is_file($drushalias_dest)) {
            $this->taskFileSystemStack()
                ->copy($drushalias_source, $drushalias_dest)
                ->run();
            $this->taskReplaceInFile($drushalias_dest)
                ->from('example.mcdev')
                ->to($this->configuration['vagrant_hostname'])
                ->run();

        }
    }

}

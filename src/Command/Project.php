<?php

namespace Mediacurrent\CiScripts\Command;

trait Project
{

	/**
     * Project Init task.
     *
     * Ensures config/config.yml exists.
     * Ensures the delegating Vagrantfile is in place.
     * Ensures the tests directory contains the ci-tests contents.
     *
     * @param string $vagrant_hostname Client project local domain [example.mcdev]
     * @param string $vagrant_ip Client project local ip [192.168.50.4]
     *
     * @return object Result
     */
    public function projectInit($vagrant_hostname = null, $vagrant_ip = null)
    {
        $this->taskProjectInit()
            ->vmInit($this->drupalvm_package)
            ->vagrantConfig($vagrant_hostname, $vagrant_ip)
            ->testsInit()
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

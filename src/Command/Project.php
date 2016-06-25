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

}

<?php

// Vagrant local development vm.
$aliases['mcdev'] = array(
  'uri' => 'example.mcdev',
  'root' => '/home/vagrant/docroot/web',
  'path-aliases' => array(
    '%drush-script' => '/home/vagrant/docroot/bin/drush',
    '%dump-dir' => '/tmp',
  ),
);

if('vagrant' !== getenv('USER')) {
  $aliases['mcdev']['remote-host'] = 'example.mcdev';
  $aliases['mcdev']['remote-user'] = 'vagrant';
  $aliases['mcdev']['ssh-options'] = '-o PasswordAuthentication=no -i ${HOME}/.vagrant.d/insecure_private_key';
}

#!/bin/bash

if [[ ! -d box ]]; then
  git clone -b feature/MCT-12-subdirectory git@bitbucket.org:mediacurrent/mis_vagrant.git ./box
fi

if [[ ! -f ./config/drupal-vm.config.yml ]]; then
  if [[ ! -d ./config ]]; then
    mkdir ./config
  fi
  cp ./box/example.config.yml ./config/drupal-vm.config.yml
fi

if [[ ! -f Vagrantfile ]]; then
  cp ./box/Vagrantfile.parent ./Vagrantfile
fi

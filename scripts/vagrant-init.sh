#!/bin/bash

SCRIPT_DIR=$(cd $(dirname "$0") && pwd -P)
BASE_DIR=$(pwd -P)
if [ ! -z $1 ]; then
  BASE_DIR=$1
fi

if [[ ! -d ${BASE_DIR}/box ]]; then
  git clone -b feature/MCT-12-subdirectory git@bitbucket.org:mediacurrent/mis_vagrant.git ${BASE_DIR}/box
fi

if [[ ! -f ${BASE_DIR}/config/drupal-vm.config.yml ]]; then
  if [[ ! -d ${BASE_DIR}/config ]]; then
    mkdir ${BASE_DIR}/config
  fi
  cp ${BASE_DIR}/box/example.config.yml ${BASE_DIR}/config/drupal-vm.config.yml
fi

if [[ ! -f ${BASE_DIR}/Vagrantfile ]]; then
  cp ${BASE_DIR}/box/Vagrantfile.parent ${BASE_DIR}/Vagrantfile
fi

#!/bin/bash

SCRIPT_DIR=$(cd $(dirname "$0") && pwd -P)
BASE_DIR=$(pwd -P)
if [ ! -z $1 ]; then
  BASE_DIR=$1
fi

if [[ ! -f ${BASE_DIR}/config/config.yml ]]; then
  if [[ ! -d ${BASE_DIR}/config ]]; then
    mkdir ${BASE_DIR}/config
  fi
  echo "Ensure the vagrant configuration file is installed."
  cp ${BASE_DIR}/vendor/mediacurrent/mis_vagrant/default.config.yml ${BASE_DIR}/config/config.yml
fi

if [[ ! -f ${BASE_DIR}/Vagrantfile ]]; then
  echo "Ensure the Vagrantfile file is installed."
  cp ${SCRIPT_DIR}/../files/Vagrantfile.parent ${BASE_DIR}/Vagrantfile
fi

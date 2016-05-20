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
  cp ${SCRIPT_DIR}/../files/example.config.yml ${BASE_DIR}/config/config.yml
fi

if [[ ! -f ${BASE_DIR}/Vagrantfile ]]; then
  cp ${SCRIPT_DIR}/../files/Vagrantfile.parent ${BASE_DIR}/Vagrantfile
fi

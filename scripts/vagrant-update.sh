#!/bin/bash

SCRIPT_DIR=$(cd $(dirname "$0") && pwd -P)
BASE_DIR=$(pwd -P)
if [ ! -z $1 ]; then
  BASE_DIR=$1
fi

source  ${SCRIPT_DIR}/vagrant-init.sh

# Ensure the latest vagrant box is downloaded
vagrant box update

# Fetch ansible roles used by Drupal VM
if [ $(command -v ansible-galaxy) ]; then
  echo "Download all required ansible roles."
  ansible-galaxy install -p ${BASE_DIR}/vendor/mediacurrent/mis_vagrant/provisioning/roles -r ${BASE_DIR}/vendor/mediacurrent/mis_vagrant/provisioning/requirements.yml --force
fi

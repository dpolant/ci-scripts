#!/bin/bash

if [[ ! -d ./tests ]]; then
  rsync -az --exclude .gitignore ./box/tests ./
fi

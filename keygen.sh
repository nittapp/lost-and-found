#!/bin/bash
# bash generate random alphanumeric string
#

mkdir logs
touch logs/nginx-error.log logs/nginx-access.logs
cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w ${1:-32} | head -n 1

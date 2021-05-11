#!/bin/bash

export ADMIN_EMAIL=admin@example.com && \
export SYMFONY_DEFAULT_ROUTE_HOST=127.0.0.1 && \
export SYMFONY_DEFAULT_ROUTE_SCHEME=http 

echo ${ADMIN_EMAIL} \
echo ${SYMFONY_DEFAULT_ROUTE_HOST} \
echo ${SYMFONY_DEFAULT_ROUTE_SCHEME}

echo 'fin'

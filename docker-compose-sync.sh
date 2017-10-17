#!/bin/bash

set -e

if [ ! -f ./docker-sync.yml ]; then
    >&2 echo "[Error] docker-sync.yml not found"
    exit 1
fi

if [ ! -f ./docker-compose-sync.yml ]; then
    >&2 echo "[Error] docker-compose-sync.yml not found"
    exit 1
fi

if [ "$1" = "start" ]; then
    echo "* Creating docker volumes"
    docker volume create --name=sync.tool

    echo "* Starting docker containers"
    docker-compose -f docker-compose-sync.yml up -d

    echo "* Cleaning up sync of docker containers"
    docker-sync clean 2> /dev/null || :

    echo "* Starting sync of docker containers"
    docker-sync start 2> /dev/null || :

    echo "* Setting up permissions"
    docker exec -it tool_tools_1 chown -R www-data: /www
elif [ "$1" = "stop" ]; then
    echo "* Stopping sync of docker containers"
    docker-sync stop 2> /dev/null || :

    echo "* Cleaning up sync of docker containers"
    docker-sync clean 2> /dev/null || :
else
    >&2 echo "[Error] Unknown argument, you should enter 'start' or 'stop' as argument"
    exit 1
fi
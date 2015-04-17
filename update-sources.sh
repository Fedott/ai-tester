#!/bin/bash

DOCKER_COMMAND=${1:-docker}

./compress.sh

${DOCKER_COMMAND} rm ai-sources
${DOCKER_COMMAND} stop ai-manager
sleep 1
${DOCKER_COMMAND} rm ai-manager
${DOCKER_COMMAND} build --tag=ai:sources docker/
${DOCKER_COMMAND} run --name=ai-sources ai:sources
${DOCKER_COMMAND} run --name=ai-manager --volumes-from=ai-sources -d ai:manager

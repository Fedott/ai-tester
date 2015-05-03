#!/bin/sh

tar --exclude-vcs --exclude="docker" --exclude="./logs/*" --exclude="./cache/*" --exclude=".idea" -cavf docker/sources.tar.bz2 .


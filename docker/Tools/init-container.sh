#/usr/bin/env sh

# This script is to be executed when the docker contianer is started


# Set UID of user www-data on guest to match the UID of the user on the host machine
usermod -u $(stat -c "%u" $1) www-data

# Set GID of group www-data on guest to match the GID of the users primary group on the host machine
groupmod -g $(stat -c "%g" $1) www-data

# Allow user www-data to log in to use development tools
usermod -s /bin/bash www-data

# Make homedir of www-data writable for www-data user for usage by development tool caches etc.
chown -R www-data: /var/www

FROM            php:5.6-fpm


########################
# Install dependencies #
########################

RUN             apt-get update && apt-get install -y \
                    git \
                    bzip2 \
                    mysql-client \
                    libpng12-dev \
                    libjpeg-dev \
                    zlib1g-dev

RUN             docker-php-ext-install pdo_mysql
RUN             docker-php-ext-configure gd --with-jpeg-dir=/usr/
RUN             docker-php-ext-install gd
RUN             docker-php-ext-install zip


###############################
# Composer, Npm, Grunt, bower #
###############################
RUN             curl -sS https://getcomposer.org/installer | php
RUN             mv composer.phar /usr/local/bin/composer
RUN             curl -sL https://deb.nodesource.com/setup | bash -
RUN             apt-get install -y nodejs
RUN             npm install npm -g.
RUN             npm install -g grunt-cli
RUN             npm install -g bower


####################
# Add config files #
####################
COPY            ./php.ini /usr/local/etc/php/php.ini
COPY            ./init-container.sh /root/init-container.sh


#######
# Run #
#######
CMD             bash /root/init-container.sh /www && php-fpm

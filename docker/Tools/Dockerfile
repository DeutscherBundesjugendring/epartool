FROM            php:7.0-fpm


########################
# Install dependencies #
########################

RUN             apt-get update && apt-get install -y \
                    git \
                    bzip2 \
                    mysql-client \
                    libpng12-dev \
                    libjpeg-dev \
                    zip \
                    zlib1g-dev \

                && docker-php-ext-install pdo_mysql \
                && docker-php-ext-configure gd --with-jpeg-dir=/usr/ \
                && docker-php-ext-install gd \
                && docker-php-ext-install zip \


##################
# Composer, Robo #
##################
                && curl -sS https://getcomposer.org/installer | php \
                && mv composer.phar /usr/local/bin/composer \
                && mkdir -p /var/www/.composer/vendor/ \
                && composer require --working-dir=/var/www/.composer/ consolidation/robo \
                && ln -s /var/www/.composer/vendor/bin/robo /usr/bin/robo \


######################
# Npm, Grunt, Bower  #
######################
                && curl -sL https://deb.nodesource.com/setup_8.x | bash - \
                && apt-get install -y nodejs \
                && npm install -g grunt-cli \
                && npm install -g webpack@3.11.0 \
                && npm install -g bower


####################
# Add config files #
####################
COPY            ./php.ini /usr/local/etc/php/php.ini
COPY            ./init-container.sh /root/init-container.sh


#######
# Run #
#######
CMD             bash /root/init-container.sh /www && php-fpm

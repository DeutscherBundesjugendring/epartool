FROM            php:7.0-fpm


########################
# Install dependencies #
########################
RUN             apt-get update && apt-get install -y \
                    libpng12-dev \
                    libjpeg-dev
RUN             docker-php-ext-install pdo_mysql
RUN             docker-php-ext-configure gd --with-jpeg-dir=/usr/
RUN             docker-php-ext-install gd
RUN             docker-php-ext-install mbstring
RUN             pecl install xdebug


####################
# Add config files #
####################
COPY            ./php.ini /usr/local/etc/php/php.ini
COPY            ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY            ./init-container.sh /root/init-container.sh


#######
# Run #
#######
CMD             bash /root/init-container.sh /www && php-fpm

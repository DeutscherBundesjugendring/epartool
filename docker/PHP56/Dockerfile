FROM            php:5.6-fpm


########################
# Install dependencies #
########################
RUN             apt-get update && apt-get install -y --no-install-recommends \
                    libpng12-dev \
                    libjpeg-dev \
                && docker-php-ext-install pdo_mysql \
                && docker-php-ext-configure gd --with-jpeg-dir=/usr/ \
                && docker-php-ext-install gd \
                && docker-php-ext-install mbstring \
                && pecl install xdebug


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

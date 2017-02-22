FROM            httpd:2.4


####################
# Configure Apache #
####################
RUN             echo "LoadModule proxy_module /usr/local/apache2/modules/mod_proxy.so" \
                    >> /usr/local/apache2/conf/httpd.conf \
                && echo "LoadModule proxy_fcgi_module /usr/local/apache2/modules/mod_proxy_fcgi.so" \
                    >> /usr/local/apache2/conf/httpd.conf \
                && echo "LoadModule rewrite_module /usr/local/apache2/modules/mod_rewrite.so" \
                    >> /usr/local/apache2/conf/httpd.conf \
                && sed -i \
                   's:#Include conf/extra/httpd-vhosts.conf:Include conf/extra/httpd-vhosts.conf:g' \
                   /usr/local/apache2/conf/httpd.conf
COPY            ./apache.vhost /usr/local/apache2/conf/extra/httpd-vhosts.conf
COPY            ./init-container.sh /root/init-container.sh


#######
# Run #
#######
CMD             bash /root/init-container.sh /www && httpd-foreground

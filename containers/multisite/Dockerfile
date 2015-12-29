FROM php:5.6-apache

RUN a2enmod rewrite

# install the PHP extensions we need
RUN apt-get update && apt-get install -y libpng12-dev libjpeg-dev && rm -rf /var/lib/apt/lists/* \
	&& docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
	&& docker-php-ext-install gd
RUN docker-php-ext-install mysqli

# VOLUME /var/www/html

RUN curl -o wordpress.tar.gz -SL https://wordpress.org/latest.tar.gz \
	&& tar -xzf wordpress.tar.gz -C /usr/src/ \
	&& rm wordpress.tar.gz \
	&& chown -R www-data:www-data /usr/src/wordpress \
  && mv /usr/src/wordpress/* /var/www/html/

COPY wp-config.php /var/www/html/
COPY .htaccess /var/www/html/

CMD ["apache2-foreground"]

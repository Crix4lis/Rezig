FROM php:7.4-cli

# create non-root user
RUN useradd -ms /bin/bash dockeruser -u 1000

# install and configure composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'e0012edf3e80b6978849f5eff0d4b4e4c79ff1609dd1e613307e16318854d24ae64f26d17af3ef0bf7cfb710ca74755a') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer \
    && chmod 755 /usr/local/bin/composer

RUN apt-get update \
    && apt-get install -y wget \
    && wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony/bin/symfony /usr/local/bin/symfony \
    && apt-get install -y vim \
    && apt-get install -y git-all

# install and enable extension
RUN pecl install xdebug-2.9.4 \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install pcntl
#    && docker-php-ext-enable pcntl
#     \
#    && docker-php-ext-install zip

# Configure bash and aliases
COPY bash/bashrc /home/dockeruser/.bashrc
COPY bash/bash_aliases /home/dockeruser/.bash_aliases
# Configure php and extensions
COPY config/xdebug.ini /usr/local/lib/php/extensions/no-debug-non-zts-20170718/xdebug.so

# Configure xDebug server
ENV PHP_IDE_CONFIG "serverName=docker-xdebug-server"

# Create volume and default dir
RUN mkdir /volume
WORKDIR /volume
VOLUME /volume

# set default user to dockeruser
USER dockeruser

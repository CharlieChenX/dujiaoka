FROM php:7.4-fpm-bullseye

COPY sources.list /etc/apt/sources.list
WORKDIR /hw_apps/mdujiaoka

RUN pecl install xdebug-3.1.6 && \
    docker-php-ext-enable xdebug && \
    pecl install redis && \
    docker-php-ext-enable redis

RUN echo 'Acquire::Check-Valid-Until no;' > /etc/apt/apt.conf.d/99no-check-valid-until && \
    apt update && \
    apt install -y supervisor libzip-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev libcurl4-openssl-dev git neovim procps && \
    
    docker-php-ext-configure gd --with-freetype && \
    docker-php-ext-install -j4 gd curl mysqli fileinfo exif pdo_mysql zip opcache bcmath && \

    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer && \
    composer config -g repo.packagist composer https://mirrors.aliyun.com/composer

COPY ./supervisord.conf  /etc/supervisor/conf.d/mdujiaoka.conf
CMD ["supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]

# 代码没有拷贝到容器, 需要启动后执行composer update

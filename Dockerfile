# syntax=docker/dockerfile:1.4
ARG FRANKENPHP_VERSION=1.7
ARG PHP_VERSION=8.4

###########################################

FROM dunglas/frankenphp:${FRANKENPHP_VERSION}-php${PHP_VERSION}-alpine AS base

ARG TZ=UTC

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo ${TZ} > /etc/timezone

# Alpine packages and Laravel \ Octane PHP extensions
RUN --mount=type=cache,target=/var/cache/apk \
    apk update \
    && apk upgrade \
    && apk add --no-cache ca-certificates curl libsodium-dev procps supervisor tzdata \
    && install-php-extensions ctype curl dom fileinfo filter hash mbstring openssl pcre pdo pdo_mysql redis session tokenizer xml \
    opcache pcntl sockets uv

# Application PHP Extensions
RUN --mount=type=cache,target=/var/cache/apk \
    install-php-extensions bcmath exif excimer gd intl vips zip && \
    docker-php-source delete \
    && rm -rf /tmp/* /var/tmp/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

###########################################

FROM base AS vendor

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install composer dependencies with cache
RUN --mount=type=cache,target=/tmp/cache \
    --mount=type=cache,target=/root/.composer/cache \
    composer install \
    --no-interaction \
    --no-ansi \
    --prefer-dist \
    --no-autoloader \
    --no-dev \
    --no-scripts

###########################################

FROM node:slim AS build

ARG APP_DIR=/var/www/html

ENV ROOT=${APP_DIR}

WORKDIR ${ROOT}

# Copy package files
COPY --link package.json package-lock.json ./

# Install node dependencies with cache
RUN --mount=type=cache,target=/root/.npm \
    npm ci --no-audit

# Copy source files
COPY --link . .

# Copy composer files (needed for vite)
COPY --link --from=vendor /app/vendor vendor

# Build node files with cache
RUN --mount=type=cache,target=/root/.npm \
    npm run build

###########################################

FROM base AS final-base

ARG WWW_UID=1000
ARG WWW_GID=1000
ARG APP_DIR=/var/www/html

ENV TERM=xterm-color \
    OCTANE_SERVER=frankenphp \
    USER=octane \
    ROOT=${APP_DIR} \
    COMPOSER_FUND=0 \
    COMPOSER_MAX_PARALLEL_HTTP=24 \
    XDG_CONFIG_HOME=${APP_DIR}/.config \
    XDG_DATA_HOME=${APP_DIR}/.data

WORKDIR ${ROOT}

SHELL ["/bin/sh", "-eou", "pipefail", "-c"]

RUN arch="$(apk --print-arch)" \
    && case "$arch" in \
    armhf) _cronic_fname='supercronic-linux-arm' ;; \
    aarch64) _cronic_fname='supercronic-linux-arm64' ;; \
    x86_64) _cronic_fname='supercronic-linux-amd64' ;; \
    x86) _cronic_fname='supercronic-linux-386' ;; \
    *) echo >&2 "error: unsupported architecture: $arch"; exit 1 ;; \
    esac \
    && wget -q "https://github.com/aptible/supercronic/releases/download/v0.2.33/${_cronic_fname}" -O /usr/bin/supercronic \
    && chmod +x /usr/bin/supercronic \
    && mkdir -p /etc/supercronic \
    && echo "*/1 * * * * php ${ROOT}/artisan schedule:run --no-interaction" > /etc/supercronic/laravel

RUN addgroup -g ${WWW_GID} ${USER} \
    && adduser -D -h ${ROOT} -G ${USER} -u ${WWW_UID} -s /bin/sh ${USER} \
    && setcap -r /usr/local/bin/frankenphp

RUN mkdir -p /var/log/supervisor /var/run/supervisor \
    && chown -R ${USER}:${USER} ${ROOT} /var/log /var/run \
    && chmod -R a+rw ${ROOT} /var/log /var/run

RUN cp ${PHP_INI_DIR}/php.ini-production ${PHP_INI_DIR}/php.ini

USER ${USER}

# Copy docker files
COPY --link --chown=${WWW_UID}:${WWW_UID} docker/supervisord.conf /etc/
COPY --link --chown=${WWW_UID}:${WWW_UID} docker/supervisord.*.conf /etc/supervisor/conf.d/
COPY --link --chown=${WWW_UID}:${WWW_UID} docker/start-container docker/healthcheck /usr/local/bin/
COPY --link --chown=${WWW_UID}:${WWW_UID} docker/php.ini ${PHP_INI_DIR}/conf.d/99-octane.ini

RUN chmod +x /usr/local/bin/start-container /usr/local/bin/healthcheck

###########################################

FROM final-base AS runner

USER ${USER}

# Copy composer files
COPY --link --chown=${WWW_UID}:${WWW_UID} --from=vendor /app/vendor vendor

# Copy source files
COPY --link --chown=${WWW_UID}:${WWW_UID} . .

# Copy node files
COPY --link --chown=${WWW_UID}:${WWW_UID} --from=build ${ROOT}/public public

# Setup storage permissions
RUN chmod -R a+rw storage

# Build autoload files
RUN composer dump-autoload \
    --no-interaction \
    --no-ansi \
    --classmap-authoritative \
    && composer clear-cache

# Expose HTTP (8000) and Reverb (8080)
EXPOSE 8000 8080

ENTRYPOINT ["start-container"]

HEALTHCHECK --start-period=60s --interval=15s --timeout=5s CMD healthcheck || exit 1

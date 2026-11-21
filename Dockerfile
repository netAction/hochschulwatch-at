FROM wordpress:cli-php8.3 AS cli
FROM wordpress:php8.3-apache

COPY --from=cli /usr/local/bin/wp /usr/local/bin/wp

# Platz für die manuelle Installation von Plugin-ZIPs im WordPress-Backend.
RUN printf '%s\n' 'upload_max_filesize = 32M' 'post_max_size = 40M' \
    > "$PHP_INI_DIR/conf.d/hochschulwatch-uploads.ini"

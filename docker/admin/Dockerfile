FROM ubuntu:14.10

RUN apt-get update

RUN apt-get install -y php5-cli php5-common php5-curl php5-json php5-dev php-pear

# Install mongo driver
RUN echo "no" | pecl install mongo
RUN echo "extension=mongo.so" > /etc/php5/mods-available/mongo.ini
RUN ln -s ../../mods-available/mongo.ini /etc/php5/cli/conf.d/20-mongo.ini

EXPOSE 80

WORKDIR /opt/project
ENTRYPOINT ["/usr/bin/php"]

CMD ["-S 0.0.0.0:80 -t web/ -r web/index.php"]


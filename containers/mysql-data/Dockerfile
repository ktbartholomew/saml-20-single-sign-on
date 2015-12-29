FROM mysql:5.7

VOLUME /var/lib/mysql
VOLUME /docker-entrypoint-initdb.d

COPY sql/ /docker-entrypoint-initdb.d/

CMD ["true"]

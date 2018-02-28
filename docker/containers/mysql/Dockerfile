FROM mysql:5.6

RUN sed -i -e"s/^#bind-address\s*=\s*127.0.0.1/bind-address = 0.0.0.0/" /etc/mysql/my.cnf

# this is to match heroku settings
RUN echo '[mysqld]' >>  /etc/mysql/my.cnf
RUN echo 'max_user_connections = 15' >>  /etc/mysql/my.cnf


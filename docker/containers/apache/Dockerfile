FROM merces/apache

ADD vhost.conf /etc/apache2/sites-available/
RUN /bin/ln -sf ../sites-available/vhost.conf /etc/apache2/sites-enabled/

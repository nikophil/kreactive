#!/usr/bin/env sh

DC_FILE=${DC_FILE:-docker-compose.yml}

composer () {
    docker-compose -f ${DC_FILE} exec --user=www-data php composer --working-dir=/var/www/symfony $*
}

console () {
    docker-compose -f ${DC_FILE} exec --user=www-data php php /var/www/symfony/bin/console $*
}

coverage() {
    docker-compose -f ${DC_FILE} exec --user=www-data php /var/www/symfony/vendor/bin/phpunit --coverage-html /var/www/symfony/app/Resources/tests
}

phpunit () {
    docker-compose -f ${DC_FILE} exec --user=www-data php /var/www/symfony/vendor/bin/phpunit --bootstrap /var/www/symfony/app/tests.bootstrap.php $*
    git co app/Resources/google_auth/credentials.json
}

phpunitNoInit () {
    docker-compose -f ${DC_FILE} exec --user=www-data php /var/www/symfony/vendor/bin/phpunit --bootstrap /var/www/symfony/app/tests-noinit.bootstrap.php $*
}

fixtures () {
    docker-compose -f ${DC_FILE} exec --user=www-data php /var/www/symfony/bin/console doctrine:database:create --if-not-exists
    docker-compose -f ${DC_FILE} exec --user=www-data php /var/www/symfony/bin/console doctrine:schema:drop --force
    docker-compose -f ${DC_FILE} exec --user=www-data php /var/www/symfony/bin/console doctrine:schema:update --force
    docker-compose -f ${DC_FILE} exec --user=www-data php /var/www/symfony/bin/console doctrine:fixtures:load -n
    docker-compose -f ${DC_FILE} exec --user=www-data php /var/www/symfony/bin/console doctrine:schema:validate
}

dsu () {
    docker-compose -f ${DC_FILE} exec --user=www-data php /var/www/symfony/bin/console doctrine:schema:update --force
    docker-compose -f ${DC_FILE} exec --user=www-data php /var/www/symfony/bin/console doctrine:schema:validate
}

dcup () {
    docker-compose -f ${DC_FILE} up -d --build
    docker-compose -f ${DC_FILE} ps
}

dcbuild () {
    docker-compose -f ${DC_FILE} pull
    docker-compose -f ${DC_FILE} build
}

rebase () {
    git fetch -p
    git rebase -p origin/master
}

emptyTmp () {
    docker-compose exec php /bin/sh -c "rm -rf /tmp/*"
}

df () {
    docker-compose exec php df -h
}

init () {
    dcup
    composer config extra.symfony.allow-contrib true
    composer install
    fixtures
}

$*

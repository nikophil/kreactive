# kreactive
test for kreactive

----

# prerequisites

Install docker & docker-composer

----

# Installation

- clone git repository
- create .env file from .env.dist and change "Docker" parameters as needed (WWWDATA_UID and WWWDATA_GID must be the one given when you type "id" in a temrinal. Use some IP_ADRR, APACHE_PORT and MYSQL_PORT that are available on your computer)
- add "test-kreactive.local" to your hosts, with the good IP (by default 172.25.0.21)
- run "./do init" (some fixtures are added, by the way)

----

# Usage

## Public URLS

### Test the access
GET http://test-kreactive.local/test

### List all the movies
GET http://test-kreactive.local/movies/
GET http://test-kreactive.local/movies/{page}

### Get the best movie
GET http://test-kreactive.local/bestMovie

### Create a user
POST http://test-kreactive.local/user

**parameters** (all parameters are mandatory):
- firstName
- lastName
- plainPassword
- email (must be unique)

### Login an user
POST http://test-kreactive.local/login_check

**parameters** (all parameters are mandatory):
- _username
- _username

## Restricted URLS

### NOTE
Login check will give you a JWT that you must use as the authorization header in all of the restricted routes :

**example** :
curl -XGET -H "Authorization: Bearer [TOKEN]" http://test-kreactive.local/api/test 

### Test the access
GET http://test-kreactive.local/api/test  

### Users adds movie as favorite
POST http://test-kreactive.local/api/user/movie

**parameter** (mandatory) :
- imdbid  

### Users adds movie as favorite
DELETE http://test-kreactive.local/api/user/movie

**parameter** (mandatory) :
- imdbid

### List the movies of connected user
GET http://test-kreactive.local/api/user/movies


### List the users whose have added one given movie
GET http://test-kreactive.local/api/movie/{imdbID}/users




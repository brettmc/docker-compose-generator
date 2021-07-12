# docker-compose generator
[![Build Status](https://travis-ci.com/brettmc/docker-compose-generator.svg?branch=master)](https://travis-ci.com/brettmc/docker-compose-generator)
[![Coverage Status](https://coveralls.io/repos/github/brettmc/docker-compose-generator/badge.svg?branch=master)](https://coveralls.io/github/brettmc/docker-compose-generator?branch=master)

A tool for populating a docker-compose file from one or more templates, using mustache-like replacement.

# Installation/usage

Composer package:
```
composer require brettmc/docker-compose-generator
```

Docker image:
```
docker run --rm -it brettmc/docker-compose-generator --help
```

# Overview
If you need to maintain multiple, slightly-different, [docker-compose](https://docs.docker.com/compose) configurations
which change per-environment, you've come to the right place.

This is being used in production in my day job for a couple of medium-sized projects, and is usually used as follows:

* git commit triggers a CI/CD pipeline
* pipeline tests, builds and tags some docker images, ready to be deployed
* pipeline executes a shell script, which tests some variables to work out which environment it is deploying to, and which files it should include:
    * tags -> prod env
    * develop -> test env
    * everything else -> build env
* generate docker-compose config by combining a main template file with one or more environment-specific files, as well as optionally a main and environment-specific .ini file, and finally any dynamic values via the `-e` switch
* pass the generated file to `docker stack deploy`

## Per-environment differences
For example, commits to development branches are built and deployed to a dev swarm. Each dev deployment gets its own database, but test and prod deployments do not.

## Multiple input files
Merge multiple `yaml` templates. For example, a `main.yml` template can be extended by having `dev.yml`
add additional services. These will be merged together before applying later operations.
If templates are provided from both `stdin` and via the `--input` option, `stdin` is processed first.
Multiple inputs provided via `--input` are processed in order.

## Variable substitution
After merging all templates, perform variable substitution across the result, based on values from `.ini` files, then environment variables and finally variables from `-e` flags.

## Define variables in numerous ways
Accepts variables from INI-style environment files, or command switches, in the following order:

* `.ini` files (eg `--ini global.ini --ini local.ini`) are processed in the order they are provided, with duplicate keys overriding earlier; then
* environment variables; then
* command-line variables (`-e FOO=foo`); and finally
* nullable command-line variables (`-o FOO=` or `-o FOO=foo`) - _if an empty value is given, it is ignored and does not clobber an earlier setting_

# Usage

`main.yml`
```
version: '3.4'
networks:
  front:
  back:
services:
  my-service:
    image: "my-service:{{TAG_OR_HASH}}"
    environment:
      BAR: "{{BAR}}"
```

`dev.yml`
```
services:
  my-service:
    ports:
      - "80:80"
  db:
    image: "postgres"
```

`prod.yml`
```
services:
  my-service:
    image: "my-service:{{TAG_OR_HASH}}"
```

`prod.ini`
```
DB_HOST=prod.db.example.com
AUTH=prod.signon.example.com
```

`dev.ini`
```
AUTH=dev.signon.example.com
```

You can then roll your own logic to work out which files to apply after `main.yml`, eg

```
#!/bin/bash
echo "generating for env: ${CI_ENV} #pre-defined variable, eg from a cicd system
case ${CI_ENV} in
  BUILD)
    HOST=docker-build-swarm-manager.example.com
    STACK=${CI_COMMIT_HASH}
    TAG_OR_HASH=${CI_COMMIT_HASH}
    BAR=something
    ;;
  PROD)
    HOST=docker-prod-swarm-manager.example.com
    STACK=prod
    TAG_OR_HASH=${CI_COMMIT_TAG}
    BAR=something-else
    ;;
esac
bin/dcgen.php generate -e HASH=${CI_COMMIT_HASH} -e BAR=${BAR} --ini ${CI_ENV}.ini --input main.yml --input ${CI_ENV}.yml > docker-compose.yml
docker -H ${HOST} stack deploy --prune --with-registry-auth -c docker-compose.yml ${STACK}
```

# Run via docker
NB, do not attach a TTY (ie, do not use docker's `-t` switch) if you are piping input
```
$ docker run --rm -i brettmc/docker-compose-generator generate -e FOO=foo -e BAR=bar < main.yml > output.yml
```
or
```
$ cat main.yml | docker run --rm -i brettmc/docker-compose-generator generate -e FOO=foo -e BAR=bar > output.yml
```
or just use volumes:
```
$ docker run --rm -v $(pwd)/conf:/srv/conf -v $(pwd)/template:/srv/template brettmc/docker-compose-generator generate --input /srv/template/main.yml --input /srv/template/dev.yml --ini /srv/conf/dev.ini -e DB_HOST=${HOST} > output.yml
```

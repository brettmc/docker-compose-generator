# docker-compose generator
[![Build Status](https://travis-ci.com/brettmc/docker-compose-generator.svg?branch=master)](https://travis-ci.com/brettmc/docker-compose-generator)
[![Coverage Status](https://coveralls.io/repos/github/brettmc/docker-compose-generator/badge.svg?branch=master)](https://coveralls.io/github/brettmc/docker-compose-generator?branch=master)

A tool for populating a docker-compose file from a template, using mustache-like replacement.

# Overview
If you need to maintain multiple, slightly-different, [docker-compose](https://docs.docker.com/compose) configurations
which change per-environment, this tool can help.

## Variable substitution
Perform variable substitution across the template, based on environment variables.

## Define variables in numerous ways
Accepts variables from INI-style environment files, command switches, or the current environment

## Remove keys you do not need
Exclude keys from final output (eg if you use a labels-based router such as [traefik](https://traefik.io) in some environments, and
rely on docker networking to expose ports in others, you can define both, and remove the one you don't need for a given configuration.

# Usage
Example template.yml
```
version: '3.4'
networks:
  front:
  back:
services:
  my-service:
    ports:
      - "80:80"
    labels:
      - "traefik.docker.network={{FOO}}"
      - "traefik.enabled=true"
      - "traefik.frontend.rule=HOST my-service.{{BAR}}.example.com"
      - "traefik.port=80"
      - "traefik.protocol=http"
    environment:
      BAR: "{{BAR}}"
```

```
$ php bin/console.php generate -e FOO=foo -e BAR=bar --ini my.ini --exclude ports < template.yml
version: '3.4'
networks:
  front: null
  back: null
services:
  my-service:
    labels:
      - 'traefik.docker.network=foo'
      - traefik.enabled=true
      - 'traefik.frontend.rule=HOST my-service.bar.example.com'
      - traefik.port=80
      - traefik.protocol=http
    environment:
      BAR: 'bar'
```
## Run via docker
NB, do not attach a TTY (ie, do not use docker's `-t` switch) if you are piping input
```
$ docker run --rm -i brettmc/docker-compose-generator generate -e FOO=foo -e BAR=bar < template.yml > generated.yml
```
or
```
$ cat template.yml | docker run --rm -i brettmc/docker-compose-generator generate -e FOO=foo -e BAR=bar > generated.yml
```

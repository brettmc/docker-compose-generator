# docker-compose generator
[![Build Status](https://travis-ci.com/brettmc/docker-compose-generator.svg?branch=master)](https://travis-ci.com/brettmc/docker-compose-generator)
[![Coverage Status](https://coveralls.io/repos/github/brettmc/docker-compose-generator/badge.svg?branch=master)](https://coveralls.io/github/brettmc/docker-compose-generator?branch=master)

A tool for populating a docker-compose file from a template, using mustache-like replacement.

# Overview
If you need to maintain multiple, slightly-different, [docker-compose](https://docs.docker.com/compose) configurations
which change per-environment, this tool can help.

## Merge multiple input templates
Merge multiple `YAML` templates. For example, a "core.yml" template can be extended by having "custom.yml"
add additional services. These will be merged together before applying later operations.
If templates are provided from both `stdin` and via the `--input` option, `stdin` is processed first.

## Variable substitution
Perform variable substitution across the template, based on environment variables.

## Define variables in numerous ways
Accepts variables from INI-style environment files, or command switches.

* `.ini` files (eg `--ini global.ini --ini local.ini`) are processed in the order they are provided, with duplicate keys overriding earlier
* env vars (`-e FOO=foo`) are applied after `.ini`

## Remove keys and values you do not need
Exclude keys and values from final output (eg if you use a labels-based router such as [traefik](https://traefik.io) in some environments, and
rely on docker networking to expose ports in others, you can define both, and remove the one you don't need for a given configuration.

## Path-based key/value-removal
Keys and values can be removed based on their path:
* ```my-service.ports``` - any matching key
* ```^services.my-service.ports``` - must match from top-level
* ```my-service.labels.remove-this-label``` - remove a single label
* ```my-services.labels.traefik*``` - remove labels by wildcard

`^` can be used to signify a top-level key.

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
      - "myCustomLabel"
    environment:
      BAR: "{{BAR}}"
```

```
$ php bin/console.php generate -e FOO=foo -e BAR=bar --ini my.ini --exclude ports --exclude my-service.labels.myCustomLabel < template.yml
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

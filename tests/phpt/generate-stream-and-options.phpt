--TEST--
generate test, should accept input from stdin + multiple input files
--FILE--
<?php
echo shell_exec('bin/console.php generate --input tests/input/template2.yml --input tests/input/template3.yml < tests/input/template.yml 2>/dev/null');
?>
--EXPECT--
version: '3.4'
networks:
  front: '{{FOO}}-front'
  back: '{{FOO}}-back'
services:
  my-service:
    ports:
      - '80:80'
    labels:
      - 'traefik.docker.network={{FOO}}'
      - traefik.enabled=true
      - 'traefik.frontend.rule=HOST my-service.{{BAR}}.example.com'
      - traefik.port=80
      - traefik.protocol=http
    environment:
      BAR: '{{BAR}}'
      BAZ: '{{BAZ}}'
      BARBAZ: '{{BAR}} and {{BAZ}}'
  my-other-service:
    ports:
      - '80:80'
    environment:
      FOO: '{{FOO}}'
  three-service:
    image: 'foo:latest'
    environment:
      FOO: '{{FOO}}'

--TEST--
generate test, replace some placeholders
--FILE--
<?php
echo shell_exec('bin/console.php generate -e FOO=foo -e BAR=bar -e BAZ=baz < tests/input/template.yml 2>/dev/null');
?>
--EXPECT--
version: '3.4'
networks:
  front: 'foo-front'
  back: 'foo-back'
services:
  my-service:
    ports:
      - '80:80'
    labels:
      - 'traefik.docker.network=foo'
      - traefik.enabled=true
      - 'traefik.frontend.rule=HOST my-service.bar.example.com'
      - traefik.port=80
      - traefik.protocol=http
    environment:
      BAR: 'bar'
      BAZ: 'baz'
      BARBAZ: 'bar and baz'


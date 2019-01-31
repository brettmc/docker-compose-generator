--TEST--
generate test, remove all label values containing *port*
--FILE--
<?php
echo shell_exec('bin/console.php generate --exclude labels.*port* tests/input/template.yml 2>/dev/null');
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
      - traefik.protocol=http
    environment:
      BAR: '{{BAR}}'
      BAZ: '{{BAZ}}'
      BARBAZ: '{{BAR}} and {{BAZ}}'


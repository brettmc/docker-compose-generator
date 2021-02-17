--TEST--
generate test, should echo the unmodified input
--FILE--
<?php
echo shell_exec('bin/dcgen generate < tests/input/main.yml 2>/dev/null');
?>
--EXPECT--
version: '3.4'
networks:
  front: '{{FOO}}-front'
  back: '{{FOO}}-back'
services:
  my-service:
    environment:
      BAR: '{{BAR}}'
      BAZ: '{{BAZ}}'
      BARBAZ: '{{BAR}} and {{BAZ}}'


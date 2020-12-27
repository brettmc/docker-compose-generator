--TEST--
generate test, replace some placeholders
--FILE--
<?php
echo shell_exec('bin/console.php generate -e FOO=foo -e BAR=bar -e BAZ=baz < tests/input/main.yml 2>/dev/null');
?>
--EXPECT--
version: '3.4'
networks:
  front: 'foo-front'
  back: 'foo-back'
services:
  my-service:
    environment:
      BAR: 'bar'
      BAZ: 'baz'
      BARBAZ: 'bar and baz'


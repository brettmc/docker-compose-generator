--TEST--
generate test, replace booleans from an ini file via --input
--FILE--
<?php
echo shell_exec('bin/dcgen generate --ini tests/input/bools.ini --input tests/input/template_with_bools.yml 2>/dev/null');
?>
--EXPECT--
version: '3.4'
services:
  my-service:
    environment:
      T: 'true'
      T2: 'true'
      F: 'false'
      ONE: '1'
      ZERO: '0'

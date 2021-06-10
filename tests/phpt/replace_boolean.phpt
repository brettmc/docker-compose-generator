--TEST--
generate test, replace booleans
--FILE--
<?php
echo shell_exec('bin/dcgen generate -e T=true -e F=false -e ZERO=0 -e ONE=1 < tests/input/template_with_bools.yml 2>/dev/null');
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

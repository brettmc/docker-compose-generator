--TEST--
generate test, replace from env var with equals signs - only split on first =, and retain the rest
--FILE--
<?php
echo shell_exec('bin/dcgen generate -e BAR=BAR== < tests/input/simple.yml 2>/dev/null');
?>
--EXPECT--
foo:
  variables:
    bar: 'BAR=='

--TEST--
empty -o setting is ignored and does not clobber setting from .ini
--FILE--
<?php
echo shell_exec('bin/dcgen generate --input tests/input/simple.yml --ini tests/input/bar.ini --env bar=baz -o BAR=');
?>
--EXPECT--
foo:
  variables:
    bar: 'bar'

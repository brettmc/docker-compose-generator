--TEST--
empty -o setting is ignored and does not clobber --env or --ini setting
--FILE--
<?php
echo shell_exec('bin/dcgen generate --input tests/input/simple.yml --ini tests/input/bar.ini -e BAR=bar -o BAR=');
?>
--EXPECT--
foo:
  variables:
    bar: 'bar'

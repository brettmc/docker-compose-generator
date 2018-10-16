#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use dcgen\Command\GenerateCommand;
use Symfony\Component\Console\Application;

$app = new Application();
$app->setName('docker-compose generator');
$app->setDefaultCommand('generate');
$app->add(new GenerateCommand());
$app->run();

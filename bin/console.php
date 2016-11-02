#!/usr/bin/env php
<?php
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../Command/TeamPasswordBackupCommand.php';
require __DIR__.'/../Command/TeamPasswordBackupDecryptCommand.php';

use Symfony\Component\Console\Application;
use Command\TeamPasswordBackupCommand;
use Command\TeamPasswordBackupDecryptCommand;

$application = new Application();
$application->add(new TeamPasswordBackupCommand());
$application->add(new TeamPasswordBackupDecryptCommand());
$application->run();
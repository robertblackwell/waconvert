<?php
// app/console
require dirname(dirname(__FILE__))."/vendor/autoload.php";

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

$application = new Application("waconvert", '1.0.0');
$application->add(new \WaConvert\ConvertCommand);
$application->setDefaultCommand("waconvert", true);
$application->run();


<?php
use GetOpt\GetOpt;
use GetOpt\Option;
use GetOpt\Command;
use GetOpt\ArgumentException;
use GetOpt\ArgumentException\Missing;

$unit_test_dir = realpath("../");
$tests_dir = dirname($unit_test_dir);
// print "<p>unit_tests directory: $unit_test_dir</p>\n";
// print "<p>tests_tests directory: $tests_dir</p>\n";
include $tests_dir."/include/bootstrap.php";
include dirname(__FILE__)."/commands/test1.php";
// include dirname(__FILE__)."/classes/ox11.php";


define('NAME', 'AwesomeApp');
define('VERSION', '1.0-alpha');

$getOpt = new GetOpt();

// define common options
$getOpt->addOptions([
   
    Option::create(null, 'version', GetOpt::NO_ARGUMENT)
        ->setDescription('Show version information and quit'),
        
    Option::create('?', 'help', GetOpt::NO_ARGUMENT)
        ->setDescription('Show this help and quit'),
    
]);

// add simple commands
$getOpt->addCommand(Command::create('test-setup', function () { 
    echo 'When you see this message the setup works.' . PHP_EOL;
})->setDescription('Check if setup works'));

// add commands
$getOpt->addCommand(new Test1Command());
// $getOpt->addCommand(new MoveCommand());
// $getOpt->addCommand(new DeleteCommand());
// $getOpt->addCommand(new Ox11Command());

// process arguments and catch user errors
try {
    try {
        $getOpt->process();
    } catch (Missing $exception) {
        // catch missing exceptions if help is requested
        if (!$getOpt->getOption('help')) {
            throw $exception;
        }
    }
} catch (ArgumentException $exception) {
    file_put_contents('php://stderr', $exception->getMessage() . PHP_EOL);
    echo PHP_EOL . $getOpt->getHelpText();
    exit;
}

// show version and quit
if ($getOpt->getOption('version')) {
    echo sprintf('%s: %s' . PHP_EOL, NAME, VERSION);
    exit;
}

// show help and quit
$command = $getOpt->getCommand();
if (!$command || $getOpt->getOption('help')) {
    echo $getOpt->getHelpText();
    exit;
}

// call the requested command
call_user_func($command->getHandler(), $getOpt);

?>

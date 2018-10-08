<?php
namespace WaConvert;

use WaConvert\Converter;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertCommand extends Command
{
	/** @return void */
	protected function configure()
	{
		$help_text =<<<EOF
The <info>%command.name%</info> converts a whiteacorn flat file database.
NOTE: the conversion is IN-PLACE so the data folder you specify will be modified.

By default this command will make a copy of the data directory before making any modifications.
The copy will be named by appending "-saved" to the end of the full canonical path of the "data_dir" argument.

Creation of the saved copy can be turned off, see options below.

The <info>data_dir</info> is a path to the flat file database top directory.
<info>options</info>
	The <info>withoutsave</info> if set the saved copy of the data_dir will NOT be created.

EOF;
		$definition =  [
				new InputArgument('data_dir', InputArgument::REQUIRED, 'The full path to the data to be converted'),
				new InputOption('withoutsave', "--without|-w", InputOption::VALUE_NONE, 'When provided will PREVENT the creation of a copy of data before starting conversion'),
				new InputOption('dryrun', "--dryrun", InputOption::VALUE_NONE, 'When provided will PREVENT any modifications to data_dir but will display the actiosn that would have been taken'),
			];
		$this
			->setName('waconvert')
			->setDescription('Convert a whiteacorn database')
			->setDefinition($definition)
			->setHelp($help_text);
	}
	/**
	* @param InputInterface  $input  A structure with args and options decoded.
	* @param OutputInterface $output An object that provides write, writeln facilities.
	* @return void
	*/
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$nosave = $input->getOption('withoutsave');
		$dryrun = $input->getOption('dryrun');
		$datapath = $input->getArgument('data_dir');
		$spl = new \SplFileInfo($datapath);
		$fp = $spl->getRealPath();
		if ($fp === false) {
			$output->writeln("data_dir : {$datapath} does not exists");
			return;
		}
		$converter = new Converter();
		$pcspl = new \SplFileInfo(dirname(dirname(__FILE__))."/preconverted_data");
		$converter->convert($spl, $pcspl, $nosave, $dryrun, $output);

		$output->writeln("we are done data: {$datapath} | {$fp}  without save: {$nosave}");
		var_dump($nosave);
		return;
	}
}

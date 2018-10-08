#!/usr/local/opt/php56/bin/php
<?php 
$inStr = $argv[1];

for( $i = 1; $i < $argc; $i++ ){
	$inStr = $argv[$i];
	$outStr = str_replace("raw","daily", $inStr);
	print $outStr . " ";
}
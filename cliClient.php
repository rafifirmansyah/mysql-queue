<?php

require_once('queue.php');
$inputHandle = fopen ("php://stdin","r");
$queue = new Queue('localhost', 'root', '', 'queue');

echo "This is the CLI client of the mysql queue.";
$line = printUsageAndGetArgument();
handleArgument($line);

function printUsageAndGetArgument(){
	global $inputHandle;
	echo 'Type any of the following commands to proceed:' . PHP_EOL;
	echo "'show', 'push', 'pop', 'exit'" . PHP_EOL . ' > ';
	return trim(fgets($inputHandle));
}

function handleArgument($line){
	global $queue, $inputHandle;
	try {	
		switch($line){
			case 'push':
				echo 'Enter payload: > ';
				$payload = trim(fgets($inputHandle));
				echo PHP_EOL; 
				echo 'newly created job id: ' . $queue->push($payload);
				break;
			case 'show':
				echo draw_text_table($queue->show());
				break;
			case 'pop':
				echo draw_text_table($queue->pop(1));
				break;
			case 'exit':
				echo 'Bye!' . PHP_EOL;
				exit(0);
				break;
			default:
				echo 'Unknown argument: ' . $line . PHP_EOL;
		}
	}
	catch (Exception $e) {
		echo 'Queue Exception occured: ' . $e->getMessage() . PHP_EOL;
	}
	$line = printUsageAndGetArgument();
	handleArgument($line);
}

// taken and modified from:
// https://www.pyrosoft.co.uk/blog/2007/07/01/php-array-to-text-table-function/
function draw_text_table($table)
{

	// Work out max lengths of each cell
	foreach($table AS $row) {
		$cell_count = 0;
		foreach($row AS $key => $cell) {
			$cell_length = max(strlen($key), strlen($cell));
			$cell_count++;
			if (!isset($cell_lengths[$key]) || $cell_length > $cell_lengths[$key]) 
				$cell_lengths[$key] = $cell_length;
		}
	}

	// Build header bar
	$bar = '+';
	$header = '|';
	$i = 0;
	foreach($cell_lengths AS $fieldname => $length) {
		$i++;
		$bar.= str_pad('', $length + 2, '-') . "+";
		$name = $fieldname;
		if (strlen($name) > $length) {
			// crop long headings
			$name = substr($name, 0, $length - 1);
		}

		$header.= ' ' . str_pad($name, $length, ' ', STR_PAD_RIGHT) . " |";
	}

	$output = '';
	$output.= $bar . "\n";
	$output.= $header . "\n";
	$output.= $bar . "\n";

	// Draw rows
	foreach($table AS $row) {
		$output.= "|";
		foreach($row AS $key => $cell) {
			$output.= ' ' . str_pad($cell, $cell_lengths[$key], ' ', STR_PAD_RIGHT) . " |";
		}

		$output.= "\n";
	}

	$output.= $bar . "\n";
	return $output;
}

?>

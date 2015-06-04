<?php

require_once 'Class/World.php';

// get initial conditions from XML file and convert it to an associative array
$xmlFile = file_get_contents('XML/input.xml', true);
$xml = simplexml_load_string($xmlFile);
$json = json_encode($xml);
$initialArray = json_decode($json,TRUE);

// create world
$world = new World($initialArray['world'], $initialArray['organisms']);

// render output to console
$world->renderWorldInConsole = true;

$numberOfIterations = $initialArray['world']['iterations'];

// iterate through the states of the world
for($i = 1; $i <= $numberOfIterations; $i++) {
	echo ($world->renderWorldInConsole)?  $i."\n" : "";
	$finalWorld = $world->iterate();
}

// write final world state to XML file
xmlOutput($initialArray['world'], $finalWorld);

function xmlOutput($settings, $world)
{
	$dom = new \DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;

	$lifeElement = $dom->createElement('life');
	$dom->appendChild($lifeElement);

	$worldElement = $dom->createElement('world');
	$lifeElement->appendChild($worldElement);

	$worldElement->appendChild($dom
		->createElement('cells', (int)$settings['cells']));
	$worldElement->appendChild($dom
		->createElement('species', (int)$settings['species']));
	$worldElement->appendChild($dom
		->createElement('iterations', (int)$settings['iterations']));

	$organismsElement = $dom->createElement('organisms');
	$lifeElement->appendChild($organismsElement);

	foreach ($world as $key => $value) {
		$ycoordinate = $key;

		foreach ($value as $xcoordinate=>$species) {
			if($species != "0") {
				$organismElement = $dom->createElement('organism');
				$organismsElement->appendChild($organismElement);
				$organismElement->appendChild($dom
					->createElement('x_pos', $xcoordinate));
				$organismElement->appendChild($dom
					->createElement('y_pos', $ycoordinate));
				$organismElement->appendChild($dom
					->createElement('species', $species));
			}
		}
	}

	$writesuccess = $dom->save( __DIR__ . '/XML/output.xml');
	
	if($writesuccess) {
		echo "\nFinal world conditions written to XML file.\n";
	}
	else {
		echo "\nError writing to file.\n";
	}
}

?>

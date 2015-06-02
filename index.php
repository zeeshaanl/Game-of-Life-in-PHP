<?php

require_once 'World.php';

// get initial conditions from XML file and convert it to an associative array
$xmlFile = file_get_contents('input.xml', true);
$xml = simplexml_load_string($xmlFile);
$json = json_encode($xml);
$initialArray = json_decode($json,TRUE);

// create world
$world = new World($initialArray['world'], $initialArray['organisms']);

$world->iterate();

?>
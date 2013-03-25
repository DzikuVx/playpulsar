<?php

$timek1=microtime();

include "../config.inc.php";
include "../db.ini.php";
include "../engine/funkcje.php";
include "../engine/translations.php";

$out = "";

$userParameters['Language'] = 'pl';

$tQuery = "SELECT * FROM ports WHERE 1";
$tQuery = \Database\Controller::getInstance()->execute($tQuery);
while($tR1 = \Database\Controller::getInstance()->fetch($tQuery)) {
	$out .= ".";
	$shipPosition['Galaxy'] = 1;
	$shipPosition['System'] = $tR1->System;
	$shipPosition['X'] = $tR1->X;
	$shipPosition['Y'] = $tR1->Y;
	$shipPosition['Docked'] = 'no';
	$portProperties = getPortProperties($shipPosition);
	$portProperties['ResetTime'] = 0;
	portProperties::sReset($shipPosition,$portProperties);
}
 
$out .= "<br />Done";
 
echo $out;

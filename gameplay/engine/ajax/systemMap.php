<?php

require_once 'ajaxHeader.php';

/**
 * Jeśli user nie jest zalogowany do gry, rzuć 403
 */
if (empty( $_SESSION ['userID'])) {
	header('HTTP/1.1 403 Forbidden');
	exit();
}

$retVal = "";

$systemID = xml::sGetValue ( $xml, "<systemID>", "</systemID>" );

$shipPosition = new shipPosition (	$userID );

if ($systemID == null || $systemID == 'null')
$systemID = $shipPosition->System;

$shipRouting = new stdClass();
$shipRouting->System = $systemID;

$map = new systemMap ( $userID, $systemID, $shipPosition );

$map->render ();

echo $map->out ();
?>
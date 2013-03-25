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
$shipProperties = new shipPosition (	$userID );

$map = new activeScanner ( $userID, $shipPosition->System, $shipPosition );

$map->render ();

echo $map->out ();
?>
<?php

require_once 'ajaxHeader.php';

$retVal = "";

$shipPosition->System = xml::sGetValue ( $xml, "<system>", "</system>" );
$shipPosition->X = xml::sGetValue ( $xml, "<x>", "</x>" );
$shipPosition->Y = xml::sGetValue ( $xml, "<y>", "</y>" );
$shipPosition->Docked = null;

$systemProperties = systemProperties::quickLoad ( $shipPosition->System );
$sectorProperties = sectorProperties::quickLoad ( $shipPosition );

$object = new remoteSectorInfo ( );

$object->render ( $sectorProperties, $systemProperties, $shipPosition );

echo $object->out ();

?>
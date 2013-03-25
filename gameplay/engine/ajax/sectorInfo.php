<?php

require_once 'ajaxHeader.php';

$retVal = "";

$shipPosition = new stdClass();

$shipPosition->System = xml::sGetValue ( $xml, "<system>", "</system>" );
$shipPosition->X = xml::sGetValue ( $xml, "<x>", "</x>" );
$shipPosition->Y = xml::sGetValue ( $xml, "<y>", "</y>" );
$shipPosition->Docked = null;

$systemProperties = systemProperties::quickLoad ( $shipPosition->System );
$sectorProperties = sectorProperties::quickLoad ( $shipPosition );

remoteSectorInfo::initiateInstance($userProperties->Language);
remoteSectorInfo::getInstance()->render ( $sectorProperties, $systemProperties, $shipPosition );

echo TranslateController::translate(remoteSectorInfo::getInstance()->out ());
<?php

//FIXME move to full panel compability

require_once 'ajaxHeader.php';

$retVal = "";

$shipPosition = new \Gameplay\Model\ShipPosition();
$shipPosition->System = $_REQUEST['System'];
$shipPosition->X = $_REQUEST['X'];
$shipPosition->Y = $_REQUEST['Y'];
$shipPosition->Docked = null;

$systemProperties = \Gameplay\Model\SystemProperties::quickLoad ( $shipPosition->System );
$sectorProperties = new \Gameplay\Model\SectorEntity($shipPosition);

remoteSectorInfo::initiateInstance($userProperties->Language);
remoteSectorInfo::getInstance()->render ( $sectorProperties, $systemProperties, $shipPosition );

echo TranslateController::translate(remoteSectorInfo::getInstance()->getRetVal());
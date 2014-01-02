<?php

require_once '../../common.php';

$out = "";

$xml = $HTTP_RAW_POST_DATA;

$system = xml::sGetValue ( $xml, "<system>", "</system>" );
$x = xml::sGetValue ( $xml, "<x>", "</x>" );
$y = xml::sGetValue ( $xml, "<y>", "</y>" );
$language = xml::sGetValue ( $xml, "<language>", "</language>" );

$shipPosition = new stdClass();

$shipPosition->System = $system;
$shipPosition->X = $x;
$shipPosition->Y = $y;
$shipPosition->Docked = null;

if (empty($language))
$language = 'en';

$userProperties = new stdClass();
$userProperties->Language = $language;

TranslateController::setDefaultLanguage($userProperties->Language);

$tSystem = array ();

$tQuery = "SELECT SystemID, Name, Width, Height, Number, Enabled, Galaxy, MapAvaible FROM systems WHERE SystemID='$system'";
$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
	$tSystem ['SystemID'] = $tR1->SystemID;
	$tSystem ['Name'] = $tR1->Name;
	$tSystem ['Height'] = $tR1->Height;
	$tSystem ['Width'] = $tR1->Width;
	$tSystem ['Number'] = $tR1->Number;
	$tSystem ['Enabled'] = $tR1->Enabled;
	$tSystem ['Galaxy'] = $tR1->Galaxy;
	$tSystem ['MapAvaible'] = $tR1->MapAvaible;
}

$tSector = $defaultSectorProperties;

$tQuery = "SELECT sectors.SectorID AS SectorID, sectors.ResetTime AS ResetTime, sectortypes.MoveCost AS MoveCost, sectortypes.Name AS Name, sectortypes.Color AS Color, sectortypes.Image AS Image, sectortypes.Visibility AS Visibility, sectortypes.Accuracy AS Accuracy, sectortypes.Resources AS Resources FROM sectors JOIN sectortypes ON sectortypes.SectorTypeID = sectors.SectorTypeID WHERE sectors.System = '$system' AND sectors.X = '$x' AND sectors.Y = '$y' LIMIT 1";
$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
	$tSector ['SectorID'] = $tR1->SectorID;
	$tSector ['Name'] = $tR1->Name;
	$tSector ['Color'] = $tR1->Color;
	$tSector ['Image'] = $tR1->Image;
	$tSector ['MoveCost'] = $tR1->MoveCost;
	$tSector ['Visibility'] = $tR1->Visibility;
	$tSector ['Accuracy'] = $tR1->Accuracy;
	$tSector ['Resources'] = $tR1->Resources;
	$tSector ['ResetTime'] = $tR1->ResetTime;
}

$out .= $tSystem ['Galaxy'] . "/" . $system . "/" . $x . "/" . $y . "<br />";
$out .= "<img src=\"../" . $tSector ['Image'] . "\" /><br />";
$out .= TranslateController::getDefault()->get($tSector ['Name']) . "<br />";
$out .= TranslateController::getDefault()->get( 'movecost') . ": " . $tSector ['MoveCost'] . "<br />";
$out .= TranslateController::getDefault()->get( 'visibility') . ": " . $tSector ['Visibility'] . "%<br />";
$out .= TranslateController::getDefault()->get( 'accuracy') . ": " . $tSector ['Accuracy'] . "%<br />";

$portProperties = new \Gameplay\Model\PortEntity($shipPosition);
if (!empty($portProperties->PortID)) {
	$out .= "Port Type Name: " . $portProperties->PortTypeName . "<br />";
}

echo $out;
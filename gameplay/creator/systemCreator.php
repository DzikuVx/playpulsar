<?
require_once '../common.php';

if (! isset ( $_GET ['system'] ))
	$_GET ['system'] = 1;
if (! isset ( $_GET ['mode'] ))
	$_GET ['mode'] = 'view';
if (! isset ( $_GET ['sectorID'] ))
	$_GET ['sectorID'] = 'null';
if (! isset ( $_GET ['portID'] ))
	$_GET ['portID'] = 'null';
	
//Pobierz parametry systemu
$tQuery = "SELECT SystemID, Name, Width, Height, Number, Enabled, Galaxy, MapAvaible FROM systems WHERE SystemID='{$_GET['system']}'";

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

//Dodawanie sektorów
if (isset ( $_GET ['sectorAdd'] )) {
	//Sprawdz, czy w tym miejscu jest już jakiś sektor
	$exists = false;
	//echo $_GET['y'];
	$w1 = \Database\Controller::getInstance()->execute ( "SELECT COUNT(SectorID) AS ile FROM sectors WHERE System='{$_GET['system']}' AND X='{$_GET['x']}' AND Y='{$_GET['y']}'" );
	while ( $r1 = \Database\Controller::getInstance()->fetch ( $w1 ) ) {
		//echo $r1['ile'];
		if ($r1->ile != 0)
			$exists = true;
	}
	
	//Jesli nie istnieje w tym miesjcu sektor......
	if (! $exists) {
		if ($_GET ['sectorID'] != 'null')
			$w1 = \Database\Controller::getInstance()->execute ( "INSERT INTO sectors(SectorTypeID,System,X,Y) VALUES('{$_GET['sectorID']}','{$_GET['system']}','{$_GET['x']}','{$_GET['y']}')" );
	} else {
		//echo $_GET['sectorID'];
		if ($_GET ['sectorID'] == 'null') {
			$w1 = \Database\Controller::getInstance()->execute ( "DELETE FROM sectorcargo WHERE SectorID IN (SELECT SectorID FROM sectors WHERE System='{$_GET['system']}' AND X='{$_GET['x']}' AND Y='{$_GET['y']}')" );
			$w1 = \Database\Controller::getInstance()->execute ( "DELETE FROM sectors WHERE System='{$_GET['system']}' AND X='{$_GET['x']}' AND Y='{$_GET['y']}'" ) ;
		} else {
			$w1 = \Database\Controller::getInstance()->execute ( "DELETE FROM sectorcargo WHERE SectorID IN (SELECT SectorID FROM sectors WHERE System='{$_GET['system']}' AND X='{$_GET['x']}' AND Y='{$_GET['y']}')" );
			$w1 = \Database\Controller::getInstance()->execute ( "UPDATE sectors SET SectorTypeID='{$_GET['sectorID']}' WHERE System='{$_GET['system']}' AND X='{$_GET['x']}' AND Y='{$_GET['y']}'" ) ;
		}
	}
}

//Dodawanie portów
if (isset ( $_GET ['portAdd'] )) {
	//Sprawdz, czy w tym miejscu jest już jakiś port
	$exists = false;
	$w1 = \Database\Controller::getInstance()->execute ( "SELECT COUNT(PortID) AS ile FROM ports WHERE System='{$_GET['system']}' AND X='{$_GET['x']}' AND Y='{$_GET['y']}'" );
	while ( $r1 = \Database\Controller::getInstance()->fetch ( $w1 ) ) {
		//echo $r1['ile'];
		if ($r1->ile != 0)
			$exists = true;
	}
	//echo $exists;
	//Jesli nie istnieje w tym miesjcu sektor......
	if (! $exists) {
		if ($_GET ['portID'] != 'null')
			$w1 = \Database\Controller::getInstance()->execute ( "INSERT INTO ports(PortTypeID,System,X,Y) VALUES('{$_GET['portID']}','{$_GET['system']}','{$_GET['x']}','{$_GET['y']}')" );
	} else {
		//echo $_GET['sectorID'];
		if ($_GET ['portID'] == 'null') {
			$w1 = \Database\Controller::getInstance()->execute ( "DELETE FROM portcargo WHERE PortID IN (SELECT PortID FROM ports WHERE System='{$_GET['system']}' AND X='{$_GET['x']}' AND Y='{$_GET['y']}')" );
			$w1 = \Database\Controller::getInstance()->execute ( "DELETE FROM ports WHERE System='{$_GET['system']}' AND X='{$_GET['x']}' AND Y='{$_GET['y']}'" );
		} else {
			$w1 = \Database\Controller::getInstance()->execute ( "DELETE FROM portcargo WHERE PortID IN (SELECT PortID FROM ports WHERE System='{$_GET['system']}' AND X='{$_GET['x']}' AND Y='{$_GET['y']}')" );
			$w1 = \Database\Controller::getInstance()->execute ( "UPDATE ports SET PortTypeID='{$_GET['portID']}' WHERE System='{$_GET['system']}' AND X='{$_GET['x']}' AND Y='{$_GET['y']}'" );
		}
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pl" xml:lang="pl">
<head>
<title>System Creator</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../css/creator.css" />
<script type="text/javascript" src="../js/creator.js"></script>
</head>
<body>

<div
	style="padding: 4px; position: absolute; border: solid; border-width: 1px; background-color: #f0f0f0; color: #000000; font-size: 9pt; visibility: hidden;"
	id="sectorInfo">
<div align="right"><img src="../gfx/close.gif" class="img_link"
	onclick="document.getElementById('sectorInfo').style.visibility = 'hidden';" /></div>
<div id="sectorInfoTxt" style="padding: 4px;"></div>
</div>

<?
//Zrób selektor systemów


echo "<form action=\"\" method=\"get\">";
echo "<select class=\"creatorSelect\" size=\"1\" name=\"system\" id=\"system\">\n";
$w1 = \Database\Controller::getInstance()->execute ( "SELECT * FROM systems ORDER BY Galaxy, Number" );
while ( $r1 = \Database\Controller::getInstance()->fetch ( $w1 ) ) {
	$fid = $r1->SystemID;
	$fnazwa = $r1->Name;
	if ($_GET ['system'] == $fid) {
		echo "<option class=\"creatorSelect\" value=\"$fid\" selected>$fid - $fnazwa - G:{$r1->Galaxy}</option>\n";
	} else {
		echo "<option class=\"creatorSelect\" value=\"$fid\">$fid - $fnazwa - G:{$r1->Galaxy}</option>\n";
	}
}
echo "</select>\n";
echo "<input type=\"submit\" class=\"button\" value=\"Go\">";
echo "</form>";

echo "<select class=\"creatorSelect\" size=\"1\" name=\"mode\" id=\"mode\">";
$temp = "";
if ($_GET ['mode'] == "view")
	$temp = "selected";
echo "<option class=\"creatorSelect\" value=\"view\" $temp>Info</option>";
$temp = "";
if ($_GET ['mode'] == "sectorAdd")
	$temp = "selected";
echo "<option class=\"creatorSelect\" value=\"sectorAdd\" $temp>Dodaj sektor</option>";
$temp = "";
if ($_GET ['mode'] == "portAdd")
	$temp = "selected";
echo "<option class=\"creatorSelect\" value=\"portAdd\" $temp>Dodaj port</option>";
echo "</select>";

echo "<br />";
echo "<select class=\"creatorSelect\" size=\"1\" name=\"sectorID\" id=\"sectorID\">";
$temp = "";
if ($_GET ['sectorID'] == "null")
	$temp = "selected";
echo "<option value=\"null\">DeepSpace</option>";
$tQuery = "SELECT * FROM sectortypes WHERE 1 ORDER BY Name";
$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
	$temp = "";
	if ($_GET ['sectorID'] == $tR1->SectorTypeID)
		$temp = "selected";
	echo "<option class=\"creatorSelect\" value=\"{$tR1->SectorTypeID}\" $temp>{$tR1->Name}</option>";
}
echo "</select>";
echo "<br />";
echo "<select class=\"creatorSelect\" size=\"1\" name=\"portID\" id=\"portID\">";
$temp = "";
if ($_GET ['portID'] == "null")
	$temp = "selected";
echo "<option value=\"null\" $temp>brak</option>";
$tQuery = "SELECT * FROM porttypes WHERE 1 ORDER BY NamePL";
$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
	$temp = "";
	if ($_GET ['portID'] == $tR1->PortTypeID)
		$temp = "selected";
	echo "<option class=\"creatorSelect\" value=\"{$tR1->PortTypeID}\" $temp>{$tR1->NamePL}</option>";
}
echo "</select>";

//Zainicjuj zmienną 
for($indexX = 1; $indexX <= $tSystem ['Width']; $indexX ++) {
	for($indexY = 1; $indexY <= $tSystem ['Height']; $indexY ++) {
		$tSector [$indexX] [$indexY] ['color'] = "000000";
		$tSector [$indexX] [$indexY] ['icon'] = "&nbsp;";
		$tSector [$indexX] [$indexY] ['iconcolor'] = "ffffff";
	}
}

$tQuery = "SELECT
    sectortypes.Color AS Color,
    sectors.X AS X,
    sectors.Y AS Y
  FROM
    sectors JOIN sectortypes ON sectortypes.SectorTypeID = sectors.SectorTypeID
  WHERE
    sectors.System = '{$_GET['system']}'
  ";
$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
	$tSector [$tR1->X] [$tR1->Y] ['color'] = $tR1->Color;
}

//Znajdz porty
$tQuery = "SELECT
    ports.State AS State,
    ports.X AS X,
    ports.Y AS Y,
    porttypes.Type AS Type
  FROM
    ports JOIN porttypes ON porttypes.PortTypeID = ports.PortTypeID
  WHERE
    ports.System = '{$_GET['system']}'
  ";
$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
	if ($tR1->Type == "port")
		$tSector [$tR1->X] [$tR1->Y] ['icon'] = "P";
	if ($tR1->Type == "station")
		$tSector [$tR1->X] [$tR1->Y] ['icon'] = "S";
	if ($tR1->State != "normal")
		$tSector [$tR1->X] [$tR1->Y] ['iconcolor'] = "f00000";
}

//Znajdz Jump Node
$tQuery = "SELECT
    *
  FROM
    nodes
  WHERE
    nodes.Active = 'yes' AND 
    (nodes.SrcSystem = '{$_GET['system']}' OR nodes.DstSystem = '{$_GET['system']}' )
  ";
$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
	if ($tR1->SrcSystem == $_GET ['system']) {
		$tSector [$tR1->SrcX] [$tR1->SrcY] ['icon'] = "N";
	} else {
		$tSector [$tR1->DstX] [$tR1->DstY] ['icon'] = "N";
	}
}

//Wyswietl mape systemu


echo "<div align=\"center\">";
echo "<table class=\"miniMap\" cellspacing=\"0\" cellpadding=\"0\">";
for($indexY = 1; $indexY <= $tSystem ['Height']; $indexY ++) {
	echo "<tr>";
	for($indexX = 1; $indexX <= $tSystem ['Width']; $indexX ++) {
		echo "<td onClick=\"performAction('system',$indexX,$indexY);\" class=\"miniMap\" style=\"cursor: pointer; background-color: #" . $tSector [$indexX] [$indexY] ['color'] . "; color: #" . $tSector [$indexX] [$indexY] ['iconcolor'] . ";\">" . $tSector [$indexX] [$indexY] ['icon'] . "</td>";
	}
	echo "</tr>";
}
echo "</table>";
echo "</div>";
?>

</body>
</html>
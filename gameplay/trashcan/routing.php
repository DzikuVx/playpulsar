<?php

require_once '../common.php';

$timek1 = microtime ();

include "../config.inc.php";
include "../db.ini.php";
include "../engine/funkcje.php";
include "../engine/translations.php";

$cache = \Cache\Session::getInstance();

$dst->System = 1;
$dst->X = 19;
$dst->Y = 5;

$route = new systemRouting ( \Database\Controller::getInstance(), $dst );

$system = systemProperties::quickLoad($dst->System);

$current->System = 1;
$current->X = 20;
$current->Y = 5;

print_r($route->next($current));

/*$routeTable = $route->getRouteTable ();

echo "<table>";
for($indexY = 1; $indexY <= $system->Height; $indexY ++) {
	echo "<tr>";
	for($indexX = 1; $indexX <= $system->Width; $indexX ++) {
		echo "<td>" . $routeTable [$indexX] [$indexY] . "</td>";
	}
	echo "</tr>";
}
echo "</table>";*/

$timek2 = microtime ();
$arr_time = explode ( " ", $timek1 );
$timek1 = $arr_time [1] + $arr_time [0];
$arr_time = explode ( " ", $timek2 );
$timek2 = $arr_time [1] + $arr_time [0];
$czas_gen = round ( $timek2 - $timek1, 4 );
echo "<br />" . $czas_gen;
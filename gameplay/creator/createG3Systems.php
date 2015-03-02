<?php

require_once '../common.php';

for($i = 1; $i <= 30; $i ++) {
	$temp = rand ( 1, 3 );
	if ($temp == 1) {
		$sx = 16;
		$sy = 16;
	}
	if ($temp == 2) {
		$sx = 20;
		$sy = 20;
	}
	if ($temp == 3) {
		$sx = 24;
		$sy = 24;
	}
	
	$name = "P3X" . rand ( 2, 9 ) . $i . rand ( 10, 90 );
	
	$tQuery = "INSERT INTO systems(Galaxy,Number,Width,Height,Enabled,MapAvaible, Name) VALUES('3','$i','$sx','$sy','yes','no','$name')";
	$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
}

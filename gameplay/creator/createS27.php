<?php

require_once '../common.php';

for($x = 1; $x <= 24; $x ++) {
	for($y = 1; $y <= 24; $y ++) {
		\Database\Controller::getInstance()->execute ( "INSERT INTO sectors(SectorTypeID,System,X,Y) VALUES('2','27','$x','$y')" ) ;
	}
}


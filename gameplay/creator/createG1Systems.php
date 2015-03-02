<?php

require_once '../common.php';

$tQuery = "INSERT INTO systems(Galaxy,Number,Width,Height,Enabled,MapAvaible) VALUES('1','6',24,24,'yes','yes')";
$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
$tQuery = "INSERT INTO systems(Galaxy,Number,Width,Height,Enabled,MapAvaible) VALUES('1','7',24,24,'yes','yes')";
$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
$tQuery = "INSERT INTO systems(Galaxy,Number,Width,Height,Enabled,MapAvaible) VALUES('1','8',24,24,'yes','yes')";
$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
$tQuery = "INSERT INTO systems(Galaxy,Number,Width,Height,Enabled,MapAvaible) VALUES('1','9',24,24,'yes','yes')";
$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
for($i = 10; $i <= 40; $i ++) {
	$tQuery = "INSERT INTO systems(Galaxy,Number,Width,Height,Enabled,MapAvaible) VALUES('1','$i',24,24,'yes','no')";
	\Database\Controller::getInstance()->execute ( $tQuery );
}

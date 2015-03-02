<?php

require_once '../common.php';

$tQuery = "SELECT * FROM systems WHERE Galaxy='1'";
$tQuery = \Database\Controller::getInstance()->execute($tQuery);
while ($tResult = \Database\Controller::getInstance()->fetch($tQuery)) {
	
	\Database\Controller::getInstance()->execute("INSERT INTO porttypes(NamePL, NameEN, Type) VALUES('".$tResult->Name." A','".$tResult->Name." A','station')");
	\Database\Controller::getInstance()->execute("INSERT INTO porttypes(NamePL, NameEN, Type) VALUES('".$tResult->Name." B','".$tResult->Name." B','station')");
	
}
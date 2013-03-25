<?php
/**
 * Klasa pozycji statku
 * Używana tabela: shippositions
 * Parametr wejściowy: UserID
 * @version $Rev: 382 $
 * @package Engine
 */
class shipPosition extends extendedItem {

	protected $tableName = "shippositions";
	protected $tableID = "UserID";
	protected $tableUseFields = array ("System", "X", "Y", "Docked" );
	protected $cacheExpire = 3600;
	
	public $System;
	public $X;
	public $Y;
	public $Docked;
	
}

<?php

namespace Gameplay\Model;

class ItemType extends Standard {
	protected $tableName = "itemtypes";
	protected $tableID = "ItemID";
	protected $tableUseFields = array('ItemID', 'Active', 'Symbol', 'NameEN', 'NamePL', 'Price', 'Experience', 'Size');
	protected $cacheExpire = 86400;

    public $ItemID;
    public $Active;
    public $Symbol;
    public $NameEN;
    public $NamePL;
    public $Price;
    public $Experience;
    public $Size;

	/**
	 * Zwraca tablicę n losowych itemów
	 *
	 * @param int $count
	 * @return array
	 */
	static function getRand($count) {
		
		$retVal = array ();
		
		$tQuery = "SELECT ItemID FROM itemtypes WHERE Active='yes' ORDER BY RAND() LIMIT " . $count;
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			array_push ( $retVal, $resultRow->ItemID );
		}
		return $retVal;
	}

}

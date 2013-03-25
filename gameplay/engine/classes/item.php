<?php
/**
 * Specjalne itemy
 *
 * @version $Rev: 454 $
 * @package Engine
 */
class item extends baseItem {
	protected $tableName = "itemtypes";
	protected $tableID = "ItemID";
	protected $tableUseFields = null;
	protected $defaultCacheExpire = 86400;
	protected $useMemcached = true;
	
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

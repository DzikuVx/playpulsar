<?php
/**
 * Wrota skoku
 *
 * @version $Rev: 455 $
 * @package Engine
 */
class jumpNode extends baseItem {
	protected $tableName = "nodes";
	protected $tableID = "NodeID";
	protected $tableUseFields = null;
	private $getAll = false;
	protected $defaultCacheExpire = 604800;
	protected $useMemcached = true;

	/**
	 * Parsowanie ID cache
	 *
	 * @param int/stdClass $ID
	 * @return string
	 */
	protected function parseCacheID($ID) {
		if (! is_numeric ( $ID )) {
			$retVal = $ID->System . "/" . $ID->X . "/" . $ID->Y;
			return md5 ( $retVal );
		} else {
			return "ID:" . $ID;
		}
	}

	/**
	 * Pobranie parametrów
	 *
	 * @param int/stdClass $ID - NodeID lub shipPosition
	 * @return boolean
	 */
	function get($ID) {

		$this->dataObject = null;

		if (! $this->getAll) {
			$whereCondition = " Active='yes' AND ";
		} else {
			$whereCondition = "";
		}

		if (! is_numeric ( $ID )) {
			$whereCondition .= "
        (
        (
        nodes.SrcSystem = '{$ID->System}' AND
        nodes.SrcX = '{$ID->X}' AND
        nodes.SrcY = '{$ID->Y}')
        OR
        (
        nodes.DstSystem = '{$ID->System}' AND
        nodes.DstX = '{$ID->X}' AND
        nodes.DstY = '{$ID->Y}')
        )
      ";
		} else {
			$whereCondition .= " nodes.NodeID = '{$ID}' ";
		}

		$tResult = \Database\Controller::getInstance()->execute ( "
      SELECT 
        NodeID, 
        Active, 
        SrcSystem, 
        SrcX, 
        SrcY, 
        DstSystem, 
        DstX, 
        DstY 
      FROM 
        nodes 
      WHERE 
        " . $whereCondition . "
      LIMIT
        1
      " );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tResult ) ) {
			$this->dataObject = $resultRow;
		}
		$this->ID = $this->parseCacheID ( $ID );
		return true;
	}

	/**
	 * Zwraca współrzędne docelowe wrót skoku
	 *
	 * @param stdClass $position - pozycja statku
	 * @param stdClass $jumpNode - jeśli null, użyty zostanie dataObject obiektu
	 */
	public function getDestination($position, $jumpNode = null) {

		if ($jumpNode == null)
		$jumpNode = $this->dataObject;

		if ($jumpNode->SrcSystem == $position->System && $jumpNode->SrcX == $position->X && $jumpNode->SrcY == $position->Y) {
			$retVal->X = $jumpNode->DstX;
			$retVal->Y = $jumpNode->DstY;
			$retVal->System = $jumpNode->DstSystem;
		} else {
			$retVal->X = $jumpNode->SrcX;
			$retVal->Y = $jumpNode->SrcY;
			$retVal->System = $jumpNode->SrcSystem;
		}

		return $retVal;
	}

}
?>
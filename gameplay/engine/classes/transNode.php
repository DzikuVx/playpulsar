<?php

/**
 * Klasa noda pomiędzy dwoma systemami,
 * przechowuje współrzędne startu
 * 
 * @version $Rev: 455 $
 * @package Engine
 *
 */
class transNode extends jumpNode {
	
	/**
	 * Pobranie transNode
	 *
	 * @param stdClass $ID
	 * @return boolean
	 */
	function get($ID) {
		
		$this->dataObject = null;
		
		$tResult = \Database\Controller::getInstance()->execute ( "
      SELECT 
        SrcSystem, 
        SrcX, 
        SrcY, 
        DstSystem, 
        DstX, 
        DstY 
      FROM 
        nodes 
      WHERE 
        Active = 'yes' AND
        ((SrcSystem='{$ID->Source}' AND DstSystem='{$ID->Destination}') OR 
        (DstSystem='{$ID->Source}' AND SrcSystem='{$ID->Destination}'))
      LIMIT
        1
      " );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tResult ) ) {
			$this->dataObject->System = $ID->Source;
			if ($resultRow->SrcSystem == $ID->Source) {
				$this->dataObject->X = $resultRow->SrcX;
				$this->dataObject->Y = $resultRow->SrcY;
			} else {
				$this->dataObject->X = $resultRow->DstX;
				$this->dataObject->Y = $resultRow->DstY;
			}
		}
		$this->ID = $this->parseCacheID ( $ID );
		return true;
	}
	
	/**
	 * Nazwa dla cache
	 *
	 * @param stdClass $ID
	 * @return string
	 */
	protected function parseCacheID($ID) {
		
		return $ID->Source . "/" . $ID->Destination;
	}
	
	public function set() {
		return true;
	}
	
	public function synchronize() {
		return true;
	}
	
	public function reload() {
		return true;
	}

}

?>
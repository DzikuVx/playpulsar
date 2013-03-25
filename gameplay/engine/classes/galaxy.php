<?php
/**
 * Klasa parametrów galaktyki
 *
 * @version $Rev: 453 $
 * @package Engine
 */
class galaxy extends baseItem {
	
	/**
	 * Pobranie losowego systemu spośród wszytkich aktywnych
	 *
	 * @return int
	 */
	static public function sGetRandomSystem() {
		$retVal = null;
		
		$tQuery = "SELECT SystemID FROM systems WHERE Enabled='yes' ORDER BY RAND() LIMIT 1";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $row = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$retVal = $row->SystemID;
		}
		
		return $retVal;
	}

	/**
	 * Pobranie losowego systemu spośród wszytkich aktywnych bez globalnie dostępnych map
	 *
	 * @return int
	 */
	static public function sGetRandomWithoutMap($galaxy) {
		$retVal = null;
		
		$tQuery = "SELECT SystemID FROM systems WHERE Enabled='yes' AND MapAvaible='no' AND Galaxy='{$galaxy}' ORDER BY RAND() LIMIT 1";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $row = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$retVal = $row->SystemID;
		}
		
		return $retVal;
	}
	
}
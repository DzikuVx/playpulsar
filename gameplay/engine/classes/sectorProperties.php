<?php

class sectorProperties extends baseItem {
	protected $tableName = "sectors";
	protected $tableID = "SectorID";
	protected $tableUseFields = array ("ResetTime" );
	protected $defaultCacheExpire = 21600;
	protected $useMemcached = true;

    /**
     * @param \Gameplay\Model\ShipPosition $shipPosition
     * @param stdClass $sectorProperties
     * @param bool $enableItemReset
     */
    static public function sResetResources(\Gameplay\Model\ShipPosition $shipPosition, $sectorProperties, $enableItemReset = true) {

		global $actualTime, $config, $itemCastProbablity, $itemCastMaxProbablity;

		/*
		 * Castowanie itemów w sektorze
		*/
		if ($enableItemReset && additional::checkRand ( $itemCastProbablity, $itemCastMaxProbablity )) {
			/*
			 * Wylosuj itema
			*/

			if (empty($sectorCargo)) {
				$sectorCargo = new sectorCargo($shipPosition);
			}

			$itemID = null;
			$tQuery = "SELECT ItemID FROM itemtypes ORDER BY RAND() LIMIT 1";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				$itemID = $tR1->ItemID;
			}
			if (!empty($itemID)) {
				$sectorCargo->insert('item', $itemID, 1);
			}
		}

		//Sprawdzenie, czy ten sektor ma jakieś zasoby
		if (($sectorProperties->Name != 'deepspace') && ($sectorProperties->ResetTime < $actualTime) && ($sectorProperties->Resources != "")) {

			if (empty($sectorCargo)) {
				$sectorCargo = new sectorCargo($shipPosition);
			}

			$resourcesArray = explode ( ",", $sectorProperties->Resources );
			reset ( $resourcesArray );
			for($tIndex = 0; $tIndex < count ( $resourcesArray ); $tIndex ++) {

				$recordExists = true;
				$Amount = $sectorCargo->getAmount('product', $resourcesArray[$tIndex]);

				if ($Amount === null) {
					$recordExists = false;
				}

				$creationDivider = 1;
				$tObject = product::quickLoad ( $resourcesArray [$tIndex] );
				$creationDivider = $tObject->CreationDivider;

				$newState = floor ( $Amount + (($config ['sector'] ['maxResources'] - $Amount) / ($config ['sector'] ['resourceDivider'] * $creationDivider)) + (rand ( - 10, 10 ) * 10) + rand ( - 9, 9 ) );
				if ($newState < 0) {
					$newState = 0;
				}
				if ($newState > $config ['sector'] ['maxResources']) {
					$newState = $config ['sector'] ['maxResources'];
				}

				/*
				 * Wprowadź nowy stan
				*/
				$sectorCargo->update('product', $resourcesArray[$tIndex], $newState );

				$sectorProperties->ResetTime = $actualTime + $config ['timeThresholds'] ['sectorReset'];
					
			}
		}
	}

	static public function quickLoad($ID, $useCache = true) {
		$item = new sectorProperties ( );
		$retVal = $item->load ( $ID, $useCache, $useCache );
		return $retVal;
	}

	/**
	 * Generuje unikalny ID sektora na postawie pozycji
	 *
	 * @param stdClass $position
	 * @return string
	 */
	function createUniqueSectorID($position) {

		$retVal = $position->System . $position->X . $position->Y;
		return md5 ( $retVal );
	}

	/**
	 * Konstruktor
	 *
	 * @param stdClass $position
	 */
	function __construct($position = null) {

		if ($position != null) {
			$this->get ( $position );
		}
	}

	/**
	 * Pobranie danych sektora
	 *
	 * @param stdClass $position
	 * @return boolean
	 */
	function get($position) {

		global $defaultSectorProperties;

		$this->dataObject = $this->toObject ( $defaultSectorProperties );

		$tResult = \Database\Controller::getInstance()->execute ( "
      SELECT 
        sectors.SectorID AS SectorID, 
        sectors.ResetTime AS ResetTime, 
        sectortypes.MoveCost AS MoveCost, 
        sectortypes.Name AS Name, 
        sectortypes.Color AS Color, 
        sectortypes.Image AS Image, 
        sectortypes.Visibility AS Visibility, 
        sectortypes.Accuracy AS Accuracy, 
        sectortypes.Resources AS Resources 
      FROM 
        sectors JOIN sectortypes ON sectortypes.SectorTypeID = sectors.SectorTypeID 
      WHERE 
        sectors.System = '{$position->System}' AND 
        sectors.X = '{$position->X}' AND 
        sectors.Y = '{$position->Y}' 
      LIMIT 1" );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tResult ) ) {
			$this->dataObject = $resultRow;
		}
		$this->ID = $this->createUniqueSectorID ( $position );
		return true;
	}

	/**
	 * Zwraca sparsowany klucz dla cache klasy
	 *   *
	 * @param stdClass $ID
	 * @return string
	 */
	protected function parseCacheID($ID) {

		return $this->createUniqueSectorID ( $ID );
	}

}
?>
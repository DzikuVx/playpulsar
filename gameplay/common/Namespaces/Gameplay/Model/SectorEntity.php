<?php

namespace Gameplay\Model;

class SectorEntity extends CustomGet {
	protected $tableName = "sectors";
	protected $tableID = "SectorID";
	protected $tableUseFields = array ("ResetTime" );
	protected $cacheExpire = 21600;

    /**
     * @var int
     */
    public $SectorID;

    /**
     * @var int
     */
    public $ResetTime;

    /**
     * @var int
     */
    public $MoveCost;

    /**
     * @var string
     */
    public $Name;

    /**
     * @var string
     */
    public $Color;

    /**
     * @var string
     */
    public $Image;

    /**
     * @var int
     */
    public $Visibility;

    /**
     * @var int
     */
    public $Accuracy;

    /**
     * @var string
     */
    public $Resources;

    /**
     * @param ShipPosition $shipPosition
     * @param SectorEntity $sectorProperties
     * @param bool $enableItemReset
     */
    static public function sResetResources(ShipPosition $shipPosition, SectorEntity $sectorProperties, $enableItemReset = true) {

		global $actualTime, $config, $itemCastProbability, $itemCastMaxProbability;

		/*
		 * Castowanie itemów w sektorze
		*/
		if ($enableItemReset && \additional::checkRand ( $itemCastProbability, $itemCastMaxProbability )) {
			/*
			 * Wylosuj itema
			*/

			if (empty($sectorCargo)) {
				$sectorCargo = new \sectorCargo($shipPosition);
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
				$sectorCargo = new \sectorCargo($shipPosition);
			}

			$resourcesArray = explode ( ",", $sectorProperties->Resources );
			reset ( $resourcesArray );
			for($tIndex = 0; $tIndex < count ( $resourcesArray ); $tIndex ++) {

				$Amount = $sectorCargo->getAmount('product', $resourcesArray[$tIndex]);

				$tObject = \product::quickLoad ( $resourcesArray [$tIndex] );
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

    //FIXME replace with dynamic method
	static public function quickLoad($ID, $useCache = true) {
		return new SectorEntity($ID);
	}

	function get() {

		global $defaultSectorProperties;

        $oDb = \Database\Controller::getInstance();

        $oData = new \stdClass();
        foreach($defaultSectorProperties as $sKey => $sValue) {
            $oData->{$sKey} = $sValue;
        }

		$tResult = $oDb->execute ( "
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
                  sectors.System = '{$this->entryId->System}' AND
                  sectors.X = '{$this->entryId->X}' AND
                  sectors.Y = '{$this->entryId->Y}'
              LIMIT 1");
		while($resultRow = $oDb->fetch($tResult)) {
            foreach($resultRow as $sKey => $sValue) {
                $oData->{$sKey} = $sValue;
            }
		}

        $this->loadData($oData, false);

		return true;
	}

    /**
     * @param ShipPosition $ID
     * @return string
     */
    protected function parseCacheID($ID) {
        return md5($ID->System . "/" . $ID->X . "/" . $ID->Y);
    }

    /**
     * @param ShipPosition $ID
     * @return int
     */
    protected function parseDbID($ID) {
        return $this->SectorID;
    }

    protected function set() {

        if (empty($this->SectorID)) {
            return;
        }

        $this->dbID = $this->SectorID;

        if (empty($this->dbID)) {
            throw new Model('Object not initialized properly');
        }

        $this->db->execute($this->formatUpdateQuery());
    }

}
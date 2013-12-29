<?php

class shipEquipment {
	protected $userID = null;
	protected $language = 'pl';
	protected $nameField = "";
	protected $tableName = "shipequipment";
	protected $addCondition = "";
	protected $changed = false;

    /**
     * @param int $userID
     * @return int
     */
    static public function sGetDamagedCount($userID) {

		$tQuery = "SELECT COUNT(*) AS ILE FROM shipequipment WHERE UserID='{$userID}' AND Damaged='1'";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);
		return \Database\Controller::getInstance()->fetch($tQuery)->ILE;
	}

	static public function sUpdateCount(&$shipProperties, $userID) {

		$tQuery = "SELECT COUNT(*) AS ile FROM shipequipment WHERE UserID='{$userID}'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$shipProperties->CurrentEquipment = $tResult->ile;
		}
	}

	/**
	 * Get number o fully operational equipment
	 * return int
	 */
	public function getOperationalCount() {

		$retVal = 0;

		$tQuery = "SELECT
			COUNT(*) AS ILE
		FROM
			shipequipment
		WHERE
			UserID='{$this->userID}' AND
			Damaged='0'
		";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		$retVal = \Database\Controller::getInstance()->fetch($tQuery)->ILE;

		return $retVal;
	}

	/**
	 * Uszkodzenie losowego wyposażenia
	 *
	 * @return boolean
	 */
	public function damageRandom() {

		$tQuery = "UPDATE shipequipment SET Damaged='1' WHERE UserID='{$this->userID}' AND Damaged='0' ORDER BY Rand() LIMIT 1";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );

		if (\Database\Controller::getInstance()->getAffectedRows () == 0) {
			return false;
		} else {
			return true;
		}

	}

	public function __destruct() {
		global $shipProperties;

		if ($this->changed) {
			shipProperties::computeDefensiveRating ( $shipProperties );
		}
	}

	/**
	 * Konstruktor
	 *
	 * @param int $userID
	 * @param string $language
	 */
	function __construct($userID, $language = 'pl') {

		$this->language = $language;
		$this->userID = $userID;
		$this->nameField = "Name" . strtoupper ( $this->language );
	}

	/**
	 * Sprawdzenie, czy dany typ equipmentu występuje w ładowni
	 *
	 * @param int/stdClass $ID
	 * @return boolean
	 */
	public function checkExists($ID) {

		if (is_numeric ( $ID )) {
			$tQuery = "SELECT COUNT(ShipEquipmentID) AS ILE FROM shipequipment WHERE UserID='{$this->userID}' AND ShipEquipmentID='{$ID}'";
		} else {
			$tQuery = "SELECT COUNT(ShipEquipmentID) AS ILE FROM shipequipment WHERE UserID='{$this->userID}' AND EquipmentID='{$ID->EquipmentID}'";
		}
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			if ($resultRow->ILE == 0) {
				$retVal = false;
			} else {
				$retVal = true;
			}
		}

		return $retVal;
	}

	/**
	 * Wstawienie wyposażenia
	 *
	 * @param stdClass $equipment
	 * @param stdClass $shipProperties
	 * @return boolean
	 */
	public function insert($equipment, $shipProperties) {

		if ($shipProperties->CurrentEquipment >= $shipProperties->MaxEquipment) {
			throw new securityException();
		}

		/*
		 * Sparawdz unikalność
		 */
		if ($equipment->Unique == 'yes') {
			if ($this->checkExists ( $equipment->EquipmentID ))
			return false;
		}

		$tQuery = "INSERT INTO shipequipment(UserID, EquipmentID) VALUES('{$this->userID}','{$equipment->EquipmentID}')";
		\Database\Controller::getInstance()->execute ( $tQuery );
		$shipProperties->CurrentEquipment += 1;
		//@todo uwzględnić przypadek, w którym rozmiar equipmentu może być większy od 1
		$this->changed = true;

		return true;
	}

	/**
	 * Naprawa wyposażenia
	 *
	 * @param int $equipment
	 * @return boolean
	 */
	public function repair($equipmentID) {

		$tQuery = "UPDATE shipequipment SET Damaged='0' WHERE ShipEquipmentID='{$equipmentID}'";
		\Database\Controller::getInstance()->execute ( $tQuery );

		$this->changed = true;

		return true;
	}

	/**
	 * Uszkodzenie wyposażenia
	 *
	 * @param int $equipmentID
	 * @return boolean
	 */
	public function damage($equipmentID) {

		$tQuery = "UPDATE shipequipment SET Damaged='1' WHERE ShipEquipmentID='{$equipmentID}'";
		\Database\Controller::getInstance()->execute ( $tQuery );

		$this->changed = true;

		return true;
	}

	/**
	 * Usunięcie całego wyposażenia okrętu
	 *
	 * @param stdClass $shipProperties
	 * @return boolean
	 */
	public function removeAll($shipProperties) {

		$tQuery = "DELETE FROM
        shipequipment
      WHERE
        UserID='{$this->userID}'";
		\Database\Controller::getInstance()->execute ( $tQuery );

		$shipProperties->CurrentEquipment = 0;
		$this->changed = true;
		return true;
	}

	/**
	 * @return boolean
	 */
	public function damageAll() {

		$tQuery = "UPDATE
        shipequipment
      SET
        Damaged='1'
      WHERE
        UserID='{$this->userID}'";
		\Database\Controller::getInstance()->execute ( $tQuery );

		$this->changed = true;
		return true;
	}

	/**
	 * Usunięcie wybranego wyposażenia
	 *
	 * @param int $ID
	 * @param stdClass $shipProperties
	 * @return boolean
	 */
	public function remove($ID, $shipProperties) {

		$tQuery = "DELETE FROM
        shipequipment
      WHERE
        ShipEquipmentID='{$ID}' AND
        UserID='$this->userID}'
      ";
		\Database\Controller::getInstance()->execute ( $tQuery );

		$shipProperties->CurrentEquipment -= 1;
		$this->changed = true;
		return true;
	}

	/**
	 * @param string $mode
	 * @param int $ID
	 * @return resource
	 */
	function get($mode = "working", $ID = null) {

		if ($ID == null) {
		    $ID = $this->userID;
        }

		switch ($mode) {
			case "all" :
				$addQuery = "";
				break;

			default :
			case "working" :
				$addQuery = "AND shipequipment.Damaged = '0'";
				break;
		}

		$tQuery = "SELECT
                equipmenttypes.*,
                equipmenttypes.{$this->nameField} AS Name,
                shipequipment.ShipEquipmentID,
                shipequipment.Damaged
            FROM
                shipequipment LEFT JOIN equipmenttypes ON equipmenttypes.EquipmentID = shipequipment.EquipmentID
            WHERE
                shipequipment.UserID='{$ID}' {$addQuery}";
        return \Database\Controller::getInstance()->execute ( $tQuery );
	}

    /**
     * @param $equipmentID
     * @return stdClass
     */
    function getSingle($equipmentID) {

		$tQuery = "SELECT
            equipmenttypes.*,
            equipmenttypes.{$this->nameField} AS Name,
            shipequipment.ShipEquipmentID,
            shipequipment.Damaged
        FROM
            shipequipment LEFT JOIN equipmenttypes ON equipmenttypes.EquipmentID = shipequipment.EquipmentID
        WHERE
            shipequipment.ShipEquipmentID='{$equipmentID}'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$retVal = $tResult;
		}
		return $retVal;
	}

	/**
	 * kupno wyposażenia statku
	 *
	 * @param int $equipmentID
	 * @throws securityException
	 */
	static public function sBuy($equipmentID) {

		global $action, $userStats, $shipProperties, $portProperties, $shipEquipment, $error;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

		if ($shipPosition->Docked == 'no') {
			throw new securityException ( );
		}

		if ($portProperties->Type != 'station') {
			throw new securityException ( );
		}

		$tEquipment = equipment::quickLoad ( $equipmentID );

		if ($userStats->Cash < $tEquipment->Price) {
			throw new securityException ( );
		}

		if ($userStats->Fame < $tEquipment->Fame) {
			throw new securityException ( );
		}

		if ($tEquipment->Type == 'equipment' && $shipEquipment->checkExists ( $tEquipment )) {
			throw new securityException ( );
		}

		/**
		 * czy port sprzedaje
		 */
		$tString = ',' . $portProperties->Equipment . ',';
		if (mb_strpos ( $tString, ',' . $equipmentID . ',' ) === false) {
			throw new securityException ( );
		}

		if (! $error) {

			$shipEquipment->insert ( $tEquipment, $shipProperties );
			userStats::decCash ( $userStats, $tEquipment->Price );
			userStats::decFame ( $userStats, $tEquipment->Fame );
			$portProperties->Cash += $tEquipment->Price;

			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:equipmentBought}' . $tEquipment->Price . '$' );
			shipProperties::computeMaxValues ( $shipProperties );
			$action = "portHangar";
		}
	}

    /**
     * @param $equipmentID
     * @throws securityException
     */
    static public function sStationRepair($equipmentID) {

		global $userStats, $shipProperties, $portProperties, $shipEquipment, $error;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

		if ($shipPosition->Docked == 'no') {
			throw new securityException ( );
		}

		$tEquipment = $shipEquipment->getSingle ( $equipmentID );

		$tRepairPrice = equipment::sGetRepairPrice ( $tEquipment->EquipmentID );

		if ($userStats->Cash < $tRepairPrice) {
			throw new securityException ( );
		}

		if (! $error) {

			$shipEquipment->repair ( $equipmentID );

			userStats::decCash ( $userStats, $tRepairPrice );
			$portProperties->Cash += $tRepairPrice;

			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:equipmentRepaired}' . $tRepairPrice . '$' );
			shipProperties::computeMaxValues ( $shipProperties );
			shipEquipmentRegistry::sRender ();
		}
	}

	static public function sSellFromCargo($weaponID) {

		global $shipCargo, $userID, $userStats, $shipProperties, $portProperties;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

		if ($shipPosition->Docked == 'no') {
			throw new securityException ( );
		}

		if ($portProperties->Type != 'station') {
			throw new securityException ( );
		}

		if ($shipCargo->getEquipmentAmount($weaponID) < 1) {
			throw new securityException ( );
		}

		/**
		 * Pobierz parametry
		 */
		$tData = equipment::quickLoad( $weaponID );

		$tPrice = floor ( $tData->Price / 2 );

		$shipCargo->decAmount($weaponID, 'equipment', 1);

		userStats::incCash ( $userStats, $tPrice );

		$portProperties->Cash -= $tPrice;
		if ($portProperties->Cash < 0) {
			$portProperties->Cash = 0;
		}

		shipProperties::updateUsedCargo ( $shipProperties );

		shipCargo::management ( $userID );
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
		\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:equipmentSold}' . $tPrice . '$' );
	}

    /**
     * @param int $equipmentID
     * @throws securityException
     */
    static public function sSell($equipmentID) {

		global $userStats, $shipProperties, $portProperties, $shipEquipment, $error;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

		if ($shipPosition->Docked == 'no') {
			throw new securityException ( );
		}

		if ($portProperties->Type != 'station') {
			throw new securityException ( );
		}

		if (! $shipEquipment->checkExists ( $equipmentID )) {
			throw new securityException ( );
		}

		if (! $error) {

			$tData = $shipEquipment->getSingle ( $equipmentID );

			if ($tData->Damaged == 0) {
				$tPrice = floor ( $tData->Price / 2 );
			} else {
				$tPrice = floor ( $tData->Price / 8 );
			}

			$shipEquipment->remove ( $equipmentID, $shipProperties );

			userStats::incCash ( $userStats, $tPrice );

			$portProperties->Cash -= $tPrice;
			if ($portProperties->Cash < 0) {
				$portProperties->Cash = 0;
			}

			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:equipmentSold}' . $tPrice . '$' );
			shipProperties::computeMaxValues ( $shipProperties );
			shipEquipmentRegistry::sRender ();
		}
	}
}
<?php

class shipWeapons {
	protected $userID = null;
	protected $language = 'pl';
	protected $nameField = "";
	protected $tableName = "shipweapons";
	protected $addCondition = "";
	protected $changed = false;

	static public function sGetDamagedCount($userID) {

		$tQuery = "SELECT COUNT(*) AS ILE FROM shipweapons WHERE UserID='{$userID}' AND Enabled IS NOT NULL AND Damaged='1'";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);
		return \Database\Controller::getInstance()->fetch($tQuery)->ILE;

	}

	/**
	 * Uszkodzenie losowaj broni gracza
	 *
	 * @return boolean
	 */
	public function damageRandom() {

		$tQuery = "UPDATE shipweapons SET Damaged='1' WHERE UserID='{$this->userID}' AND Damaged='0' ORDER BY Rand() LIMIT 1";
		\Database\Controller::getInstance()->execute($tQuery);

		if (\Database\Controller::getInstance()->getAffectedRows() == 0) {
			return false;
		} else {
			return true;
		}

	}

	/**
	 * Przeliczenie OffRating Statku
	 *
	 * @param \Gameplay\Model\ShipProperties $shipProperties
	 */
	public function computeOffensiveRating(\Gameplay\Model\ShipProperties $shipProperties) {

		if (empty($shipProperties)) {
			$shipProperties = new stdClass();
		}

		$tQuery = "SELECT
		    SUM(
		    (wt.ShieldMax +
		    wt.ShieldMin +
		    wt.ArmorMin +
		    wt.ArmorMax) / 8
		    ) AS ILE
		  FROM
		    shipweapons AS sw JOIN weapontypes AS wt ON wt.WeaponID = sw.WeaponID
		  WHERE
		    UserID = '{$this->userID}'
		";

		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$shipProperties->OffRating = round ( $resultRow->ILE );
		}
	}

	public function __destruct() {
		global $shipProperties;
		if ($this->changed) {
			$this->computeOffensiveRating ( $shipProperties );
		}
	}

	/**
	 * Pobranie najwyższego sequence uzbrojenia statku
	 *
	 * @param int $userID
	 * @return int
	 */
	static private function sGetMaxSequence($userID) {

		$tQuery = "SELECT MAX(Sequence) AS ILE FROM shipweapons WHERE UserID='{$userID}'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		$retVal = \Database\Controller::getInstance()->fetch ( $tQuery )->ILE;

		if (empty ( $retVal )) {
			$retVal = 0;
		}

		return $retVal;
	}

	/**
	 * Wstawienie uzborojenia do statku
	 *
	 * @param stdClass $weapon
	 * @param \Gameplay\Model\ShipProperties $shipProperties
	 * @return boolean
	 */
	public function insert($weapon, \Gameplay\Model\ShipProperties $shipProperties) {

		if ($shipProperties->CurrentWeapons >= $shipProperties->MaxWeapons) {
		    return false;
        }

		if ($weapon->Ammo == null) {
			$tString = "null";
		} else {
			$tString = "'" . $weapon->Ammo . "'";
		}

		$tSequence = static::sGetMaxSequence ( $this->userID ) + 1;

		$tQuery = "INSERT INTO shipweapons(UserID, WeaponID, Ammo, Sequence) VALUES('{$this->userID}','{$weapon->WeaponID}',$tString,'{$tSequence}')";
		\Database\Controller::getInstance()->execute ( $tQuery );
		$shipProperties->CurrentWeapons += 1;

		$this->changed = true;

		return true;
	}

	/**
	 * Wstawienie amunicji do broni
	 *
	 * @param int $shipWeaponID
	 * @param int $ammo
	 * @return boolean
	 */
	public function reload($shipWeaponID, $ammo) {

		$tQuery = "UPDATE shipweapons SET Ammo='$ammo' WHERE ShipWeaponID='{$shipWeaponID}' AND UserID='{$this->userID}' LIMIT 1";
		\Database\Controller::getInstance()->execute ( $tQuery );

		$this->changed = true;

		return true;
	}

    /**
     * @param int $ID
     * @param \Gameplay\Model\ShipProperties $shipProperties
     * @return bool
     */
    public function remove($ID, \Gameplay\Model\ShipProperties $shipProperties) {

		$tQuery = "DELETE FROM
                shipweapons
            WHERE
                ShipWeaponID='{$ID}' AND UserID='$this->userID}'";
		\Database\Controller::getInstance()->execute ( $tQuery );

		$shipProperties->CurrentWeapons -= 1;
		$this->changed = true;
		return true;
	}

    /**
     * @param stdClass $shipProperties
     * @return bool
     */
    public function removeAll($shipProperties) {

		$tQuery = "DELETE FROM
        shipweapons
      WHERE
        UserID='{$this->userID}'";
		\Database\Controller::getInstance()->execute ( $tQuery );
		$shipProperties->CurrentWeapons = 0;
		$this->changed = true;
		return true;
	}

	/**
	 * Uszkodzenie całego uzbrojenia gracza
	 *
	 * @return boolean
	 */
	public function damageAll() {

		$tQuery = "UPDATE
        shipweapons
      SET
        Damaged='1'
      WHERE
        UserID='{$this->userID}'";
		\Database\Controller::getInstance()->execute ( $tQuery );
		$this->changed = true;
		return true;
	}

	/**
	 * Wyłączenie uzbrojenia
	 *
	 * @param int $ID
	 * @return boolean
	 */
	public function disable($ID) {

		$tQuery = "UPDATE
        shipweapons
      SET
        Enabled = '0'
      WHERE
        ShipWeaponID='{$ID}' LIMIT 1";
		\Database\Controller::getInstance()->execute ( $tQuery );
		$this->changed = true;
		return true;
	}

	/**
	 * Włączenie uzbrojenia
	 *
	 * @param int $ID
	 * @return boolean
	 */
	public function enable($ID) {

		$tQuery = "UPDATE
        shipweapons
      SET
        Enabled = '1'
      WHERE
        ShipWeaponID='{$ID}' LIMIT 1";
		\Database\Controller::getInstance()->execute ( $tQuery );
		$this->changed = true;
		return true;
	}

	/**
	 * Uszkodzenie uzbrojenia
	 *
	 * @param int $ID
	 * @return boolean
	 */
	public function damage($ID) {

		$tQuery = "UPDATE
        shipweapons
      SET
        Damaged = '1'
      WHERE
        ShipWeaponID='{$ID}'
      LIMIT 1";
		\Database\Controller::getInstance()->execute ( $tQuery );
		$this->changed = true;
		return true;
	}

	/**
	 * Naprawienie uzbrojenia
	 *
	 * @param int $ID
	 * @return boolean
	 */
	public function repair($ID) {

		$tQuery = "UPDATE
        shipweapons
      SET
        Damaged = '0'
      WHERE
        ShipWeaponID='{$ID}' LIMIT 1";
		\Database\Controller::getInstance()->execute ( $tQuery );
		$this->changed = true;
		return true;
	}

	public function repairAll() {

		$tQuery = "UPDATE
        shipweapons
      SET
        Damaged = '0'
      WHERE
        UserID='{$this->userID}'";
		\Database\Controller::getInstance()->execute ( $tQuery );
		$this->changed = true;
		return true;
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
	 * Przełączenie stanu wybranej broni
	 *
	 * @param int $ID
	 * @return boolean
	 */
	function switchState($ID) {

		if ($this->userID == null)
		return false;

		global $error;

		$tId = null;
		$tEnabled = null;
		$tQuery = "SELECT UserID, Enabled FROM shipweapons WHERE ShipWeaponID='$ID'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$tId = $tR1->UserID;
			$tEnabled = $tR1->Enabled;
		}

		if ($tId != $this->userID)
		$error = true;

		if (! $error) {
			$this->changed = true;

            if ($tEnabled == '0') {
			    $tNewState = '1';
            } else {
			    $tNewState = '0';
            }

			$tQuery = "UPDATE shipweapons SET Enabled='$tNewState' WHERE ShipWeaponID='$ID'";
			\Database\Controller::getInstance()->execute ( $tQuery );
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get number o fully operational weapons
	 * return int
	 */
	public function getOperationalCount() {

		$tQuery = "SELECT
			COUNT(*) AS ILE
		FROM
			shipweapons
		WHERE
			UserID='{$this->userID}' AND
			Enabled='1' AND
			Damaged='0' AND
			(Ammo IS NULL OR Ammo > 0)
		";

        $oDb = \Database\Controller::getInstance();

		$tQuery = $oDb->execute ( $tQuery );
		return $oDb->fetch($tQuery)->ILE;
	}

	/**
	 * Pobranie uzbrojenia okrętu
	 *
	 * @param string $mode
	 * @param int $ID
	 * @return resource
	 */
	function get($mode = "enabled", $ID = null) {

		if ($ID == null)
		$ID = $this->userID;

		switch ($mode) {
			case "all" :
				$addQuery = "";
				break;

			case "fireable" :
				$addQuery = "AND shipweapons.Enabled = '1' AND shipweapons.Damaged = '0'";
				break;

			default :
			case "enabled" :
				$addQuery = "AND shipweapons.Enabled = '1'";
				break;
		}

		$tQuery = "SELECT
        weapontypes.*,
        weapontypes.{$this->nameField} AS Name,
        weapontypes.Ammo AS MaxAmmo,
        shipweapons.Ammo AS Ammo,
        shipweapons.Enabled AS Enabled,
        shipweapons.Sequence AS Sequence,
        shipweapons.ShipWeaponID,
        shipweapons.Damaged
      FROM
        shipweapons LEFT JOIN weapontypes ON weapontypes.WeaponID = shipweapons.WeaponID
      WHERE
        shipweapons.UserID='{$ID}' {$addQuery}
      ORDER BY
        shipweapons.Sequence ASC
        ";
		$retVal = \Database\Controller::getInstance()->execute ( $tQuery );
		return $retVal;
	}

	/**
	 * Pobranie pojedynczej broni na podstawie jej ID
	 *
	 * @param int $weaponID
	 * @return stdClass
	 */
	public function getSingle($weaponID) {

		$retVal = null;

		$tQuery = "SELECT
        weapontypes.*,
        weapontypes.{$this->nameField} AS Name,
        weapontypes.Ammo AS MaxAmmo,
        shipweapons.Ammo AS Ammo,
        shipweapons.Enabled AS Enabled,
        shipweapons.Sequence AS Sequence,
        shipweapons.ShipWeaponID,
        shipweapons.Damaged
      FROM
        shipweapons LEFT JOIN weapontypes ON weapontypes.WeaponID = shipweapons.WeaponID
      WHERE
        shipweapons.ShipWeaponID='{$weaponID}' AND
        shipweapons.UserID='{$this->userID}'
      LIMIT 1
        ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$retVal = $tResult;
		}
		return $retVal;
	}

    /**
     * @param \Gameplay\Model\ShipProperties $shipProperties
     * @param int $userID
     */
    static public function sUpdateCount(\Gameplay\Model\ShipProperties $shipProperties, $userID) {

		$tQuery = "SELECT COUNT(*) AS ile FROM shipweapons WHERE UserID='{$userID}'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$shipProperties->CurrentWeapons = $tResult->ile;
		}
	}

	/**
	 * Sprawdzenie, czy statek posiada broń o takim ID
	 *
	 * @param int $ID
	 * @return boolean
	 */
	public function checkExists($ID) {

		$retVal = false;

		$tQuery = "SELECT * FROM shipweapons WHERE UserID='{$this->userID}' AND ShipWeaponID='{$ID}' LIMIT 1";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			if (! empty ( $tResult )) {
				$retVal = true;
			}
		}

		return $retVal;
	}

	/**
	 * Pobranie broni o mniejszym sequence
	 *
	 * @param int $currentSequence
	 * @return stdClass
	 */
	private function getPrevSequence($currentSequence) {

        $retVal = new stdClass();
		$retVal->Sequence = null;
		$retVal->ShipWeaponID = null;

		$tQuery = "SELECT ShipWeaponID, Sequence FROM shipweapons WHERE UserID='{$this->userID}' AND Sequence<'{$currentSequence}' ORDER BY Sequence DESC LIMIT 1";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$retVal->ShipWeaponID = $tResult->ShipWeaponID;
			$retVal->Sequence = $tResult->Sequence;
		}

		return $retVal;
	}

	/**
	 * Pobranie broni o większym sequence
	 *
	 * @param int $currentSequence
	 * @return stdClass
	 */
	private function getNextSequence($currentSequence) {

        $retVal = new stdClass();
		$retVal->Sequence = null;
		$retVal->ShipWeaponID = null;

		$tQuery = "SELECT ShipWeaponID, Sequence FROM shipweapons WHERE UserID='{$this->userID}' AND Sequence>'{$currentSequence}' ORDER BY Sequence ASC LIMIT 1";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$retVal->ShipWeaponID = $tResult->ShipWeaponID;
			$retVal->Sequence = $tResult->Sequence;
		}

		return $retVal;
	}

	/**
	 * @param int $shipWeaponID
	 * @param int $sequence
	 */
	private function setSequence($shipWeaponID, $sequence) {

		$tQuery = "UPDATE shipweapons SET Sequence='{$sequence}' WHERE ShipWeaponID='{$shipWeaponID}' AND UserID='{$this->userID}'";
		\Database\Controller::getInstance()->execute ( $tQuery );

	}

    /**
     * @param int $shipWeaponID
     * @return bool
     * @throws securityException
     */
    static public function sMoveUp($shipWeaponID) {

		global $error, $shipWeapons;

		$tData = $shipWeapons->getSingle ( $shipWeaponID );

		/*
		 * Warunki bezpieczeństwa
		 */

		if (empty ( $tData )) {
			throw new securityException ( );
		}

		if (! $error) {

			$tOtherWeapon = $shipWeapons->getPrevSequence ( $tData->Sequence );

			if (empty ( $tOtherWeapon->Sequence )) {
				return false;
			}

			$shipWeapons->setSequence ( $shipWeaponID, $tOtherWeapon->Sequence );
			$shipWeapons->setSequence ( $tOtherWeapon->ShipWeaponID, $tData->Sequence );

			shipWeaponsRegistry::sRender ();
		}
		return true;
	}

    /**
     * @param int $shipWeaponID
     * @return bool
     * @throws securityException
     */
    static public function sMoveDown($shipWeaponID) {

		global $error, $shipWeapons;

		$tData = $shipWeapons->getSingle($shipWeaponID);

		/*
		 * Warunki bezpieczeństwa
		 */
		if (empty ( $tData )) {
			throw new securityException ( );
		}

		if (! $error) {

			$tOtherWeapon = $shipWeapons->getNextSequence ( $tData->Sequence );

			if (empty ( $tOtherWeapon->Sequence )) {
				return false;
			}

			$shipWeapons->setSequence ( $shipWeaponID, $tOtherWeapon->Sequence );
			$shipWeapons->setSequence ( $tOtherWeapon->ShipWeaponID, $tData->Sequence );

			shipWeaponsRegistry::sRender ();
		}
		return true;
	}

    /**
     * @param int $weaponID
     * @throws securityException
     */
    static public function sSell($weaponID) {

		global $userID, $userStats, $shipProperties, $portProperties, $shipWeapons, $error;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

		static::sUpdateCount ( $shipProperties, $userID );

		if ($shipPosition->Docked == 'no') {
			throw new securityException ( );
		}

		if ($portProperties->Type != 'station') {
			throw new securityException ( );
		}

		if (! $shipWeapons->checkExists ( $weaponID )) {
			throw new securityException ( );
		}

		if (! $error) {

			/**
			 * Pobierz parametry
			 */
			$tData = $shipWeapons->getSingle ( $weaponID );

			if ($tData->Damaged == 0) {
				$tPrice = floor ( $tData->Price / 2 );
			} else {
				$tPrice = floor ( $tData->Price / 8 );
			}

			$shipWeapons->remove ( $weaponID, $shipProperties );
            $userStats->incCash($tPrice);

			$portProperties->Cash -= $tPrice;
			if ($portProperties->Cash < 0) {
				$portProperties->Cash = 0;
			}

			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:weaponSold}' . $tPrice . '$' );
			shipWeaponsRegistry::sRender ();
			$shipWeapons->computeOffensiveRating ( $shipProperties );
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

		if ($shipCargo->getWeaponAmount($weaponID) < 1) {
			throw new securityException ( );
		}

		/**
		 * Pobierz parametry
		 */
		$tData = weapon::quickLoad( $weaponID );

		$tPrice = floor ( $tData->Price / 2 );

		$shipCargo->decAmount($weaponID, 'weapon', 1);
        $userStats->incCash($tPrice);

		$portProperties->Cash -= $tPrice;
		if ($portProperties->Cash < 0) {
			$portProperties->Cash = 0;
		}

        \Gameplay\Model\ShipProperties::updateUsedCargo ( $shipProperties );

		shipCargo::management ( $userID );
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
		\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:weaponSold}' . $tPrice . '$' );
	}

    /**
     * @param int $shipWeaponID
     * @throws securityException
     */
    static public function sReload($shipWeaponID) {

		global $error, $shipWeapons, $portProperties, $userStats;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

		$tData = $shipWeapons->getSingle($shipWeaponID);

		/*
		 * Warunki bezpieczeństwa
		 */

		if ($shipPosition->Docked == 'no') {
			throw new securityException ( );
		}

		if ($portProperties->Type != 'station') {
			throw new securityException ( );
		}

		$tReloadPrice = weapon::sGetReloadPrice ( $tData->WeaponID, $tData->Ammo );

		if ($userStats->Cash < $tReloadPrice) {
			throw new securityException();
		}

		if (empty ( $tData->MaxAmmo )) {
			throw new securityException();
		}

		if ($tData->Ammo == $tData->MaxAmmo) {
			throw new securityException();
		}

		/**
		 * Od 2011-05-24 broń można przeładować w dowolnej stacji
		 */

		if (! $error) {

			$shipWeapons->reload ( $shipWeaponID, $tData->MaxAmmo );

            $userStats->decCash($tReloadPrice);
			$portProperties->Cash += $tReloadPrice;

			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:weaponReloadedFor}' . $tReloadPrice . '$' );
			shipWeaponsRegistry::sRender ();
		}

	}

    /**
     * @param int $shipWeaponID
     * @throws securityException
     */
    static public function sRepair($shipWeaponID) {

		global $error, $shipWeapons, $portProperties, $userStats;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

		$tData = $shipWeapons->getSingle ( $shipWeaponID );

		/*
		 * Warunki bezpieczeństwa
		 */

		if ($shipPosition->Docked == 'no') {
			throw new securityException ( );
		}

		$tRepairPrice = weapon::sGetRepairPrice ( $tData->WeaponID );

		if ($userStats->Cash < $tRepairPrice) {
			throw new securityException ( );
		}

		if (! $error) {
			$shipWeapons->repair ( $shipWeaponID );

            $userStats->decCash($tRepairPrice);
			$portProperties->Cash += $tRepairPrice;

			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:weaponRepairedFor}' . $tRepairPrice . '$' );
			shipWeaponsRegistry::sRender ();
		}

	}

    /**
     * @param int $weaponID
     * @throws securityException
     */
    static public function sBuy($weaponID) {

		global $userID, $action, $userStats, $shipProperties, $portProperties, $shipWeapons;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

		static::sUpdateCount ( $shipProperties, $userID );

		if ($shipPosition->Docked == 'no') {
			throw new securityException ( );
		}

		if ($portProperties->Type != 'station') {
			throw new securityException ( );
		}

		$tWeapon = weapon::quickLoad ( $weaponID );

		if ($userStats->Cash < $tWeapon->Price) {
			throw new securityException ( );
		}

		if ($userStats->Fame < $tWeapon->Fame) {
			throw new securityException ( );
		}

		/**
		 * czy port sprzedaje
		 */
		$tString = ',' . $portProperties->Weapons . ',';
		if (mb_strpos ( $tString, ',' . $weaponID . ',' ) === false) {
			throw new securityException ( );
		}

		$shipWeapons->insert($tWeapon, $shipProperties);
        $userStats->decCash($tWeapon->Price);
        $userStats->decFame($tWeapon->Fame);
		$portProperties->Cash += $tWeapon->Price;

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:weaponBought}' . $tWeapon->Price . '$' );
		$action = "portHangar";
		$shipWeapons->computeOffensiveRating ( $shipProperties );
	}
}

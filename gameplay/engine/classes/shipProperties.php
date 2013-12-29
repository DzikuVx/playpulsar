<?php

class shipProperties extends baseItem {
	protected $tableName = "userships";
	protected $tableID = "UserID";
	protected $tableUseFields = array ('Targetting', 'Scan', 'Cloak', "ArmorStrength", "ArmorPiercing", "Emp", "EmpMax", "Maneuver", "OffRating", "DefRating", "ShipName", "ShipID", "ShieldRegeneration", "PowerRegeneration", "ArmorRegeneration", "Shield", "ShieldMax", "Armor", "ArmorMax", "Power", "PowerMax", "Cargo", "CargoMax", "CurrentWeapons", "MaxWeapons", "CurrentEquipment", "MaxEquipment", "Gather", "Turns", "Speed", "RookieTurns", "SpecializationID", "CanRepairWeapons", "CanRepairEquipment", "Squadron",'CanActiveScan','CanWarpJump' );
	protected $defaultCacheExpire = 1440;
	protected $useMemcached = true;

	/**
	 * @param stdClass $shipProperties
	 * @param shipProperties $shipPropertiesObject
     * @return boolean
	 */
	static public function sDropRookie($shipProperties, $shipPropertiesObject) {

		global $userID;

		$shipProperties->RookieTurns = 0;

		$shipPropertiesObject->synchronize($shipProperties, true, true);

		shipExamine ( $userID, $userID );
		\Gameplay\Panel\PortAction::getInstance()->clear();
		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', '{T:opSuccess}');
		return true;
	}

	//@todo dodać cloak multiplier do włączania i wyłączania

    /**
     * @param \General\Templater $template
     * @param string $return
     */
    static public function sRenderRepairButtons($template, $return = 'hangar') {
		global $shipProperties, $config, $userStats;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

		if ($shipPosition->Docked == 'yes') {
			if ($shipProperties->Shield < $shipProperties->ShieldMax && $userStats->Cash > ($config ['repairCost'] ['shield'] * ($shipProperties->ShieldMax < $shipProperties->Shield))) {
				$template->add ( 'ShieldRepairButton', \General\Controls::renderImgButton ( 'repair', "Playpulsar.gameplay.execute('stationRepair','Shield','{$return}',null,null);", TranslateController::getDefault()->get ( 'RepairFor' ) . ($config ['repairCost'] ['shield'] * ($shipProperties->ShieldMax - $shipProperties->Shield)) . '$' ) );
			} else {
				$template->add ( 'ShieldRepairButton', '&nbsp;' );
			}
			if ($shipProperties->Armor < $shipProperties->ArmorMax && $userStats->Cash > ($config ['repairCost'] ['armor'] * ($shipProperties->ArmorMax < $shipProperties->Armor))) {
				$template->add ( 'ArmorRepairButton', \General\Controls::renderImgButton ( 'repair', "Playpulsar.gameplay.execute('stationRepair','Armor','{$return}',null,null);", TranslateController::getDefault()->get ( 'RepairFor' ) . ($config ['repairCost'] ['armor'] * ($shipProperties->ArmorMax - $shipProperties->Armor)) . '$' ) );
			} else {
				$template->add ( 'ArmorRepairButton', '&nbsp;' );
			}
			if ($shipProperties->Power < $shipProperties->PowerMax && $userStats->Cash > ($config ['repairCost'] ['power'] * ($shipProperties->PowerMax < $shipProperties->Power))) {
				$template->add ( 'PowerRepairButton', \General\Controls::renderImgButton ( 'repair', "Playpulsar.gameplay.execute('stationRepair','Power','{$return}',null,null);", TranslateController::getDefault()->get ( 'RepairFor' ) . ($config ['repairCost'] ['power'] * ($shipProperties->PowerMax - $shipProperties->Power)) . '$' ) );
			} else {
				$template->add ( 'PowerRepairButton', '&nbsp;' );
			}
			if ($shipProperties->Emp > 0 && $userStats->Cash > ($config ['repairCost'] ['emp'] * ($shipProperties->EmpMax < $shipProperties->Emp))) {
				$template->add ( 'EmpRepairButton', \General\Controls::renderImgButton ( 'repair', "Playpulsar.gameplay.execute('stationRepair','Emp','{$return}',null,null);", TranslateController::getDefault()->get ( 'RepairFor' ) . ($config ['repairCost'] ['emp'] * $shipProperties->Emp) . '$' ) );
			} else {
				$template->add ( 'EmpRepairButton', '&nbsp;' );
			}
		} else {
			$template->add ( 'ShieldRepairButton', '&nbsp;' );
			$template->add ( 'ArmorRepairButton', '&nbsp;' );
			$template->add ( 'PowerRepairButton', '&nbsp;' );
			$template->add ( 'EmpRepairButton', '&nbsp;' );
		}

	}

    /**
     * @return bool
     * @throws securityException
     */
    static public function sStationRepair() {

		global $action, $value, $config, $portProperties, $shipProperties, $subaction, $userStats;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

		/*
		 * Warunki
		 */
		if ($shipPosition->Docked != 'yes') {
			throw new securityException ( );
		}

		if (empty ( $portProperties->PortID )) {
			throw new securityException ( );
		}

		/*
		 * Dokonaj naprawy
		 */
		switch ($subaction) {

			case 'Shield' :
				$tPrice = ($shipProperties->ShieldMax - $shipProperties->Shield) * $config ['repairCost'] ['shield'];

				if ($tPrice < 0) {
					throw new securityException ( );
				}

				if ($userStats->Cash < $tPrice) {
					throw new securityException ( );
				}

				$shipProperties->Shield = $shipProperties->ShieldMax;
				$userStats->Cash -= $tPrice;
				break;

			case 'Armor' :
				$tPrice = ($shipProperties->ArmorMax - $shipProperties->Armor) * $config ['repairCost'] ['armor'];

				if ($tPrice < 0) {
					throw new securityException ( );
				}

				if ($userStats->Cash < $tPrice) {
					throw new securityException ( );
				}

				$shipProperties->Armor = $shipProperties->ArmorMax;
				$userStats->Cash -= $tPrice;
				break;

			case 'Power' :
				$tPrice = ($shipProperties->PowerMax - $shipProperties->Power) * $config ['repairCost'] ['power'];

				if ($tPrice < 0) {
					throw new securityException ( );
				}

				if ($userStats->Cash < $tPrice) {
					throw new securityException ( );
				}

				$shipProperties->Power = $shipProperties->PowerMax;
				$userStats->Cash -= $tPrice;
				break;

			case 'Emp' :
				$tPrice = $shipProperties->Emp * $config ['repairCost'] ['emp'];

				if ($tPrice < 0) {
					throw new securityException ( );
				}

				if ($userStats->Cash < $tPrice) {
					throw new securityException ( );
				}

				$shipProperties->Emp = 0;
				$userStats->Cash -= $tPrice;
				break;

			default :
				throw new securityException ( );
				break;

		}

		if ($value == 'summary') {
			shipEquipmentRegistry::sRender ();
		} elseif ($value == 'hangar') {
			$action = "portHangar";
		}

		return true;
	}

	/**
	 * Sprawdzenie, czy statek nie jest uszkodzony przez EMP
	 *
	 * @param stdClass $shipProperties
	 * @return boolean
	 */
	static public function sCheckMalfunction($shipProperties) {
		return additional::checkRand ( $shipProperties->Emp, $shipProperties->EmpMax );
	}

	/**
	 * @param stdClass $shipProperties
	 * @param stdClass $userStats
	 * @param stdClass $otherShipProperties
	 * @param stdClass $otherUserStats
	 * @param stdClass $sectorProperties
	 * @return boolean
	 */
	static public function sGetVisibility($shipProperties, $userStats, $otherShipProperties, $otherUserStats, $sectorProperties) {

		$percentage = $sectorProperties->Visibility + $userStats->Level - $otherUserStats->Level + $shipProperties->Scan - $otherShipProperties->Cloak;

		if ($percentage < 1) {
			$percentage = 1;
		}

		if ($percentage > 99) {
			$percentage = 99;
		}

		return additional::checkRand ( $percentage, 100 );
	}

	/**
	 * Def rating
	 *
	 * @param stdClass $shipProperties
	 */
	static function computeDefensiveRating($shipProperties) {
		if (!empty($shipProperties)) {
			$shipProperties->DefRating = floor ( ($shipProperties->Shield + $shipProperties->Armor) / 100 );
		}
	}

	/**
	 * Przeliczenie wykorzystanej przestrzeni ładowni
	 *
	 * @param stdClass $shipProperties
	 */
	static function updateUsedCargo($shipProperties) {

		$item = new shipCargo ( $shipProperties->UserID );
		$shipProperties->Cargo = $item->getUsage ();
		unset($item);
	}

	/**
	 * Ustawienie wartości maksymalnych jako aktualne
	 *
	 * @param stdClass $shipProperties
	 */
	static function setFromFull($shipProperties) {

		$shipProperties->Shield = $shipProperties->ShieldMax;
		$shipProperties->Armor = $shipProperties->ArmorMax;
		$shipProperties->Power = $shipProperties->PowerMax;
		$shipProperties->Emp = 0;
	}

	/**
	 * Statyczne przeliczenie wszystkich max values
	 * @param int $userID
	 */
	static public function sQuickRecompute($userID) {

		$shipPropertiesObject = new shipProperties();
		$shipProperties = $shipPropertiesObject->load($userID,true, true);
		self::computeMaxValues($shipProperties);
		shipWeapons::sUpdateCount($shipProperties, $userID);
		shipEquipment::sUpdateCount($shipProperties, $userID);
		$shipPropertiesObject->synchronize($shipProperties, true, true);
        \shipProperties::sFlushCache($userID);
	}

    /**
     * @param \stdClass $shipProperties
     * @param int $userID
     */
    static public function sRecomputeValues($shipProperties, $userID) {
		self::computeMaxValues($shipProperties);
		shipWeapons::sUpdateCount($shipProperties, $userID);
		shipEquipment::sUpdateCount($shipProperties, $userID);
	}

	/**
	 * Obliczenie pełnych parametrów okrętu
	 *
	 * @param stdClass $shipProperties
	 */
	static function computeMaxValues($shipProperties) {

		$tShip = ship::quickLoad ( $shipProperties->ShipID );

		$shipProperties->ShieldMax = $tShip->Shield;
		$shipProperties->ArmorMax = $tShip->Armor;
		$shipProperties->PowerMax = $tShip->Power;
		$shipProperties->Speed = $tShip->Speed;
		$shipProperties->Maneuver = $tShip->Maneuver;
		$shipProperties->CargoMax = $tShip->Cargo;
		$shipProperties->MaxEquipment = $tShip->Space;
		$shipProperties->MaxWeapons = $tShip->Weapons;
		$shipProperties->Scan = $tShip->Scan;
		$shipProperties->Cloak = $tShip->Cloak;
		$shipProperties->Gather = $tShip->Gather;
		$shipProperties->ArmorStrength = $tShip->ArmorStrength;
		$shipProperties->ArmorPiercing = $tShip->ArmorPiercing;
		$shipProperties->ShieldRegeneration = $tShip->ShieldRegeneration;
		$shipProperties->ArmorRegeneration = $tShip->ArmorRegeneration;
		$shipProperties->PowerRegeneration = $tShip->PowerRegeneration;
		$shipProperties->ShieldRepair = $tShip->ShieldRepair;
		$shipProperties->ArmorRepair = $tShip->ArmorRepair;
		$shipProperties->PowerRepair = $tShip->PowerRepair;
		$shipProperties->EmpMax = $tShip->Emp;
		$shipProperties->Targetting = $tShip->Targetting;
		$shipProperties->CanWarpJump = $tShip->CanWarpJump;
		$shipProperties->CanActiveScan = $tShip->CanActiveScan;

		unset ( $tShip );

		/*
		 * Dokonaj pętli po equipmencie
		 */

		$equipmentList = new shipEquipment ( $shipProperties->UserID );

		$tResult = $equipmentList->get ( "working" );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tResult ) ) {
			$shipProperties->ShieldMax += $resultRow->Shield;
			$shipProperties->ArmorMax += $resultRow->Armor;
			$shipProperties->PowerMax += $resultRow->Power;
			$shipProperties->EmpMax += $resultRow->Emp;
			$shipProperties->Speed += $resultRow->Speed;
			$shipProperties->Maneuver += $resultRow->Maneuver;
			$shipProperties->CargoMax += $resultRow->Cargo;
			$shipProperties->MaxEquipment += $resultRow->Space;
			$shipProperties->MaxWeapons += $resultRow->Weapons;
			$shipProperties->Scan += $resultRow->Scan;
			$shipProperties->Cloak += $resultRow->Cloak;
			$shipProperties->Gather += $resultRow->Gather;
			$shipProperties->ArmorStrength += $resultRow->ArmorStrength;
			$shipProperties->ArmorPiercing += $resultRow->ArmorPiercing;
			$shipProperties->ShieldRegeneration += $resultRow->ShieldRegeneration;
			$shipProperties->ArmorRegeneration += $resultRow->ArmorRegeneration;
			$shipProperties->PowerRegeneration += $resultRow->PowerRegeneration;
			$shipProperties->ShieldRepair += $resultRow->ShieldRepair;
			$shipProperties->ArmorRepair += $resultRow->ArmorRepair;
			$shipProperties->PowerRepair += $resultRow->PowerRepair;
			$shipProperties->Targetting += $resultRow->Targetting;
			$shipProperties->CanWarpJump += $resultRow->CanWarpJump;
			$shipProperties->CanActiveScan += $resultRow->CanActiveScan;
		}

		self::sCutProperties ( $shipProperties );

	}

	/**
	 * Obcięcie wartości statku do dostępnych maksymalnych
	 *
	 * @param stdClass $shipProperties
	 */
	static public function sCutProperties($shipProperties) {

		if ($shipProperties->Shield > $shipProperties->ShieldMax) {
			$shipProperties->Shield = $shipProperties->ShieldMax;
		}
		if ($shipProperties->Armor > $shipProperties->ArmorMax) {
			$shipProperties->Armor = $shipProperties->ArmorMax;
		}
		if ($shipProperties->Power > $shipProperties->PowerMax) {
			$shipProperties->Power = $shipProperties->PowerMax;
		}

	}

	/**
	 * Metoda generująca tury i fame
	 *
	 * @param stdClass $shipProperties
	 * @param stdClass $userTimes
	 * @return boolean
	 */
	public function generateTurns($shipProperties, $userTimes) {

		global $config, $actualTime, $userStats;

		/*
		 * Reset fame
		 */
		if ($actualTime - $userTimes->FameReset > $config ['fame'] ['resetThreshold']) {

			$tValue = (floor ( ($actualTime - $userTimes->FameReset) / $config ['fame'] ['resetThreshold'] ) * $config ['fame'] ['multiplier']);

			/*
			 * Capping
			 */
			if ($tValue > $config ['fame'] ['cap']) {
				$tValue = $config ['fame'] ['cap'];
			}

			$userStats->Fame += $tValue;

			$userTimes->FameReset = $actualTime;
		}

		if ($actualTime - $userTimes->TurnReset > $config ['timeThresholds'] ['turnsReset']) {

			/*
			 * Aby nie dodawać max, a tylko zwykłą ilość
			 */
			if (empty($userTimes->TurnReset)) {
				$userTimes->TurnReset = time();
				return true;
			}

			$shipProperties->Turns += floor ( ($actualTime - $userTimes->TurnReset) / $config ['timeThresholds'] ['turnsReset'] ) * $shipProperties->Speed * $config ['turns'] ['multiplier'];

			//Capping
			if ($shipProperties->Turns > ($shipProperties->Speed * $config ['turns'] ['capLimit']))
			$shipProperties->Turns = $shipProperties->Speed * $config ['turns'] ['capLimit'];

			$act_d = date ( "d", $actualTime );
			$act_m = date ( "m", $actualTime );
			$act_y = date ( "Y", $actualTime );
			$act_h = date ( "H", $actualTime );
			$act_i = 0;
			$act_s = 0;

			//@todo zapis nie uwzględnia interwałów mniejszych niż 1h

			//Zapisz ilość tur;
			$userTimes->TurnReset = mktime ( $act_h, $act_i, $act_s, $act_m, $act_d, $act_y );

		}
		return true;
	}

	/**
	 * Metoda automatycznego naprawiania statku
	 *
	 * @param stdClass $shipProperties
	 * @param stdClass $userTimes
	 * @param boolean $display - czy wyświetlać informację o naprawie
	 * @return boolean
	 */
    //FIXME remove unused parameter
	public function autoRepair($shipProperties, $userTimes, $display = true) {

		global $config;

		$actualTime = time ();

		$repaired = false;

		/*
		 * Czy dokonać naprawy
		 */
		if ($actualTime - $userTimes->LastRepair >= $config ['timeThresholds'] ['shipRepair']) {

			/*
			 * Czy naprawiać Shield
			 */
			if ($shipProperties->Shield < $shipProperties->ShieldMax && $shipProperties->ShieldRegeneration > 0) {

				$toRepair = ($actualTime - $userTimes->LastRepair) * $shipProperties->ShieldRegeneration;

				$shipProperties->Shield += $toRepair;
				if ($shipProperties->Shield > $shipProperties->ShieldMax)
				$shipProperties->Shield = $shipProperties->ShieldMax;

				$repaired = true;
			}

			/*
			 * Czy naprawiać Armor
			 */
			if ($shipProperties->Armor < $shipProperties->ArmorMax && $shipProperties->ArmorRegeneration > 0) {

				$toRepair = ($actualTime - $userTimes->LastRepair) * $shipProperties->ArmorRegeneration;

				$shipProperties->Armor += $toRepair;
				if ($shipProperties->Armor > $shipProperties->ArmorMax)
				$shipProperties->Armor = $shipProperties->ArmorMax;

				$repaired = true;
			}

			/*
			 * Czy generować Power
			 */
			if ($shipProperties->Power < $shipProperties->PowerMax && $shipProperties->PowerRegeneration > 0) {

				$toRepair = ($actualTime - $userTimes->LastRepair) * $shipProperties->PowerRegeneration;

				$shipProperties->Power += $toRepair;
				if ($shipProperties->Power > $shipProperties->PowerMax)
				$shipProperties->Power = $shipProperties->PowerMax;

				$repaired = true;
			}

			/*
			 * Czy naprawiać EMP
			 */
			if ($shipProperties->Emp > 0) {
				$toRepair = ($actualTime - $userTimes->LastRepair) * $config ['emp'] ['repairRatio'];
				$shipProperties->Emp -= $toRepair;
				if ($shipProperties->Emp < 0) {
					$shipProperties->Emp = 0;
				}
				$repaired = true;
			}

			if ($repaired) {
				self::computeDefensiveRating ( $shipProperties );
			}
			$userTimes->LastRepair = time ();

		}

		return true;
	}

	/**
	 * Pobranie parametrów statku
	 *
	 * @param int $ID - ID gracza
	 * @return true
	 */
	function get($ID) {

		global $userProperties;

		if (empty($userProperties)) {
			$userProperties = new stdClass();
		}

		if (empty ( $userProperties->Language )) {
			$userProperties->Language = 'en';
		}

		$nameField = "Name" . strtoupper ( $userProperties->Language );

		$tQuery = "SELECT
		    userships.UserID AS UserID,
		    userships.ShipName AS ShipName,
        userships.ShipID AS ShipID,
        userships.ShieldRegeneration AS ShieldRegeneration,
        userships.PowerRegeneration AS PowerRegeneration,
        userships.ArmorRegeneration AS ArmorRegeneration,
        userships.Shield AS Shield,
        userships.ShieldMax AS ShieldMax,
        userships.Armor AS Armor,
        userships.ArmorMax AS ArmorMax,
        userships.Power AS Power,
        userships.PowerMax AS PowerMax,
        userships.DefRating AS DefRating,
        userships.OffRating AS OffRating,
        userships.Emp AS Emp,
        userships.EmpMax AS EmpMax,
        userships.Cargo AS Cargo,
        userships.CargoMax AS CargoMax,
        userships.CurrentWeapons AS CurrentWeapons,
        userships.MaxWeapons AS MaxWeapons,
        shiptypes.WeaponSize AS WeaponSize,
        userships.CurrentEquipment AS CurrentEquipment,
        userships.MaxEquipment AS MaxEquipment,
        userships.Gather AS Gather,
        userships.Turns AS Turns,
        userships.Speed AS Speed,
        userships.Maneuver AS Maneuver,
        userships.Scan AS Scan,
        userships.Cloak AS Cloak,
        userships.Targetting AS Targetting,
        userships.CanWarpJump,
        userships.CanActiveScan,
        userships.ArmorStrength AS ArmorStrength,
        userships.ArmorPiercing AS ArmorPiercing,
        userships.RookieTurns AS RookieTurns,
        userships.SpecializationID AS SpecializationID,
        userships.CanRepairWeapons AS CanRepairWeapons,
        userships.CanRepairEquipment AS CanRepairEquipment,
        shiptypes.Price AS Price,
        shiptypes.$nameField AS ShipTypeName,
        specializations.$nameField AS SpecializationName,
        userships.Squadron AS Squadron
    FROM
      userships LEFT JOIN specializations ON specializations.SpecializationID = userships.SpecializationID
      JOIN shiptypes USING(ShipID)
    WHERE userships.UserID='$ID'";

		$tResult = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tResult ) ) {
			$this->dataObject = $resultRow;
		}
		$this->ID = $ID;
		return true;
	}

	/**
	 * Update off i deff rating okrętu
	 *
	 * @param int $userID
	 */
	static public function sUpdateRating(/** @noinspection PhpUnusedParameterInspection */
        $userID) {
		global $shipWeapons, $shipProperties;
		$shipWeapons->computeOffensiveRating ( $shipProperties );
		self::computeDefensiveRating ( $shipProperties );
	}

	/**
	 * Zwraca wartość statku gracza
	 *
	 * @param int $userID
	 * @return int
	 */
	static public function sGetValue($userID) {

		/*
		 * Pobierz wartość statku
		 */
		$shipPropertiesObject = new shipProperties ( );
		$shipProperties = $shipPropertiesObject->load ( $userID, true, true );
		$retVal = $shipProperties->Price;

		$tQuery = "SELECT
                SUM(weapontypes.Price) AS Value
            FROM
                shipweapons JOIN weapontypes USING(WeaponID)
            WHERE
                shipweapons.UserID = '{$userID}'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$retVal += $tResult->Value;
		}

		/*
		 * Pobierz wartość equipu
		 */
		$tQuery = "SELECT
                SUM(equipmenttypes.Price) AS Value
            FROM
                shipequipment JOIN equipmenttypes USING(EquipmentID)
            WHERE
                shipequipment.UserID = '{$userID}'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$retVal += $tResult->Value;
		}

		return $retVal;
	}

    /**
     * @param $shipID
     * @throws securityException
     */
    static public function sBuy($shipID) {

		global $shipPropertiesObject, $shipCargo, $shipWeapons, $userProperties, $action, $userStats, $shipProperties, $portProperties, $shipEquipment;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

		if ($shipPosition->Docked == 'no') {
			throw new securityException ( );
		}

		if ($portProperties->Type != 'station') {
			throw new securityException ( );
		}

		$tShip = ship::quickLoad ( $shipID );

		$currentShipValue = floor ( self::sGetValue ( $userProperties->UserID ) / 2 );

		if ($userStats->Cash + $currentShipValue < $tShip->Price) {
			throw new securityException ( );
		}

		if ($userStats->Fame < $tShip->Fame) {
			throw new securityException ( );
		}

		/**
		 * czy port sprzedaje
		 */
		$tString = ',' . $portProperties->Ships . ',';
		if (mb_strpos ( $tString, ',' . $shipID . ',' ) === false) {
			throw new securityException ( );
		}

		$shipProperties->ShipID = $shipID;
		$shipPropertiesObject->synchronize ( $shipProperties, true, true );
		$shipProperties = $shipPropertiesObject->load ( $userProperties->UserID, true, true );

		userStats::decCash ( $userStats, $tShip->Price - $currentShipValue );
		userStats::decFame ( $userStats, $tShip->Fame );
		$portProperties->Cash += $tShip->Price;
		$shipEquipment->removeAll ( $shipProperties );
		$shipWeapons->removeAll ( $shipProperties );
		$shipCargo->removeAll ( $shipProperties );

		self::computeMaxValues ( $shipProperties );
		self::setFromFull ( $shipProperties );
		$shipWeapons->computeOffensiveRating ( $shipProperties );
		self::computeDefensiveRating ( $shipProperties );

		$action = "portHangar";

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:shipBought}' . $tShip->Price . '$' );
	}
}

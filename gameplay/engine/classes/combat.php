<?php

class combat {

	/**
	 * Wynik
	 *
	 * @var string
	 */
	protected $retVal = '';

	/**
	 * Język outputu
	 *
	 * @var string
	 */
	protected $Language = 'en';

	/**
	 * Gracz
	 *
	 * @var int
	 */
	protected $userID = null;

	/**
	 * Parametry gracza
	 *
	 * @var combatShip
	 */
	protected $player = null;

	/**
	 * Sojusz gracza
	 * @var int
	 */
	protected $playerAlliance = null;

	/**
	 * Tablica 'wrogów'
	 *
	 * @var combatShip[]
	 */
	private $enemies;

	/**
	 * @var Translate
	 */
	protected $t;

	/**
	 * Enter description here...
	 *
	 * @var \Gameplay\Model\ShipPosition
	 */
	protected $shipPosition = null;

    /**
     * @var \Gameplay\Model\SectorEntity
     */
    protected $sectorProperties = null;

	protected $authCode;

	static protected $weaponCriticalHit = false;

	public function getAuthCode() {
		return $this->authCode;
	}

	/**
	 * Magic
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->retVal;
	}

	/**
	 * Wylosuj wroga do oddania strzału
	 *
	 * @param string $method - metoda wyboru
	 * @return combatShip
	 */
	protected function getEnemy($method = 'random') {

		$retVal = null;

		/*
		 * Sprawdź, czy jest jakikolwiek do którego można strzelić:
		 */
		$tFireable = array ();
		$tFound = false;
		foreach ( $this->enemies as $tKey => $tEnemy ) {
			if ($tEnemy->shipProperties->Armor > 0) {
				$tFound = true;
				array_push ( $tFireable, $tKey );
			}
		}

		if (!$tFound) {
			return null;
		}

		/**
		 * Wylosuj wroga
		 */
		switch ($method) {

			//@todo inne metody losowania przeciwników

			default :
				$tIndex = rand ( 0, count ( $tFireable ) - 1 );
				$retVal = $this->enemies [$tFireable [$tIndex]];
				break;
		}

		return $retVal;
	}

	protected function fireWeapon($tWeapon, combatShip $tTarget, $tPreparedUpdate) {

		self::$weaponCriticalHit = false;

		/**
		 * Uszkodzenia emp statku
		 */
		if ($this->player->shipProperties->checkMalfunction()) {
			array_push ( $this->player->weaponFireResult, new weaponFireResult ( 'player', $this->player->userProperties->Name, $tTarget->userProperties->Name, $tWeapon->WeaponID, 'empDamage' ) );
			return false;
		}

		/*
		 * Sprawdz, czy broń może wystrzelić
		 */
		if ($this->player->shipProperties->Power < $tWeapon->PowerUsage) {
			array_push ( $this->player->weaponFireResult, new weaponFireResult ( 'player', $this->player->userProperties->Name, $tTarget->userProperties->Name, $tWeapon->WeaponID, 'noPower' ) );
			return false;
		}

		if ($tWeapon->MaxAmmo != null && $tWeapon->Ammo < 1) {
			array_push ( $this->player->weaponFireResult, new weaponFireResult ( 'player', $this->player->userProperties->Name, $tTarget->userProperties->Name, $tWeapon->WeaponID, 'noAmmo' ) );
			return false;
		}

		/*
		 * Zmiejsz energię statku
		 */
		$this->player->shipProperties->Power -= $tWeapon->PowerUsage;

		/*
		 * jeśli broń na amunicję, zmiejsc liczbę amunicji
		 */
		if ($tWeapon->MaxAmmo != null) {

			mysqli_stmt_bind_param($tPreparedUpdate, 'i', $tWeapon->ShipWeaponID);
			$tResult = mysqli_stmt_execute($tPreparedUpdate);

			if (empty($tResult)) {
				throw new \Database\Exception ( mysqli_error (\Database\Controller::getInstance()->getHandle()), mysqli_errno (\Database\Controller::getInstance()->getHandle()) );
			}
		}

		/*
		 * Sprawdz, czy broń trafiła
		 */
		$Accuracy = self::computeWeaponAccuracy ( $tWeapon, $this->player, $tTarget, $this->sectorProperties );
		if (additional::checkRand ( $Accuracy, 100 )) {
			/*
			 * Obliczenia po trafieniu
			 */
			$tShieldDamage = 0;
			$tArmorDamage = 0;
			$tPowerDamage = 0;
			$tEmpDamage = 0;

			if ($tTarget->shipProperties->Shield > 0) {

				/*
				 * uderzenie idzie w osłonę
				 */
				$tShieldDamage += self::computeShieldDamage ( $tWeapon, $this->player, $tTarget );

				if (self::checkShieldPenetration ()) {
					$tArmorDamage += self::computePenetrationDamage ( $tWeapon );
				}

			} elseif ($tTarget->shipProperties->Armor > 0) {
				/*
				 * uderzenie idzie w pancerz
				 */

				$tArmorDamage += self::computeArmorDamage ( $tWeapon, $this->player, $tTarget );

				if ($tWeapon->PowerMax > 0) {
					/*
					 * Zdrajnuj power
					 */
					$tPowerDamage += self::computePowerDamage ( $tWeapon, $this->player, $tTarget );
				}

				if ($tWeapon->EmpMax > 0) {
					/*
					 * Dodaj uszkodzenia EMP
					 */
					$tEmpDamage += self::computeEmpDamage ( $tWeapon, $this->player, $tTarget );
				}

			}

			/**
			 * Jeśli doszło do przebicia osłon, zobacz czy uszkodzoć broń i wyposażenie
			 */
			if ($tArmorDamage > 0) {
				if (static::checkWeaponsDamage ( $tWeapon, $this->player, $tTarget )) {
					if ($tTarget->shipWeapons->damageRandom ()) {
						array_push ( $tTarget->weaponFireResult, new weaponFireResult ( 'target', $this->player->userProperties->Name, $tTarget->userProperties->Name, $tWeapon->WeaponID, 'weaponDamaged' ) );
						array_push ( $this->player->weaponFireResult, new weaponFireResult ( 'player', $this->player->userProperties->Name, $tTarget->userProperties->Name, $tWeapon->WeaponID, 'weaponDamaged' ) );
					}
				}

				if (static::checkEquipmentDamage ( $tWeapon, $this->player, $tTarget )) {
					if ($tTarget->shipEquipment->damageRandom ()) {
						array_push ( $tTarget->weaponFireResult, new weaponFireResult ( 'target', $this->player->userProperties->Name, $tTarget->userProperties->Name, $tWeapon->WeaponID, 'equipmentDamaged' ) );
						array_push ( $this->player->weaponFireResult, new weaponFireResult ( 'player', $this->player->userProperties->Name, $tTarget->userProperties->Name, $tWeapon->WeaponID, 'equipmentDamaged' ) );
					}
				}
			}

			$tTarget->shipProperties->Shield -= $tShieldDamage;
			if ($tTarget->shipProperties->Shield < 0) {
				$tTarget->shipProperties->Shield = 0;
			}
			$tTarget->shipProperties->Armor -= $tArmorDamage;
			if ($tTarget->shipProperties->Armor < 0) {
				$tTarget->shipProperties->Armor = 0;
			}
			$tTarget->shipProperties->Power -= $tPowerDamage;
			if ($tTarget->shipProperties->Power < 0) {
				$tTarget->shipProperties->Power = 0;
			}
			$tTarget->shipProperties->Emp += $tEmpDamage;
			if ($tTarget->shipProperties->Emp > $tTarget->shipProperties->EmpMax) {
				$tTarget->shipProperties->Emp = $tTarget->shipProperties->EmpMax;
			}

			/*
			 * zapisz wynik strzelania
			 */
			$weaponResultType = 'hit';
			if (self::$weaponCriticalHit) {
				$weaponResultType = 'critic';
			}

			array_push ( $this->player->weaponFireResult, new weaponFireResult ( 'player', $this->player->userProperties->Name, $tTarget->userProperties->Name, $tWeapon->WeaponID, $weaponResultType, $tShieldDamage, $tArmorDamage, $tPowerDamage, $tEmpDamage ) );
			array_push ( $tTarget->weaponFireResult, new weaponFireResult ( 'target', $this->player->userProperties->Name, $tTarget->userProperties->Name, $tWeapon->WeaponID, $weaponResultType, $tShieldDamage, $tArmorDamage, $tPowerDamage, $tEmpDamage ) );

			/**
			 * Czy cel został zniszczony
			 */
			if ($tTarget->shipProperties->Armor < 1) {

				/*
				 * Update statystyk
				 */
				$this->player->userStats->Kills += 1;
				$this->player->userStats->Fame += 1;
				$tTarget->userStats->Deaths += 1;

				$plusExp = self::sComputeExperienceIncome($this->player->userStats, $tTarget->userStats);

                $this->player->userStats->incExperience($plusExp);
                $this->player->userStats->incCash(floor($tTarget->userStats->Cash / 2));

				/**
				 * Wstaw wpis do newsagency
				 */
				new newsAgencyMessage ( 2, $tTarget->userProperties, $this->player->userProperties, $this->shipPosition );

				array_push ( $this->player->weaponFireResult, new weaponFireResult ( 'player', $this->player->userProperties->Name, $tTarget->userProperties->Name, $tWeapon->WeaponID, 'kill' ) );
				array_push ( $tTarget->weaponFireResult, new weaponFireResult ( 'target', $this->player->userProperties->Name, $tTarget->userProperties->Name, $tWeapon->WeaponID, 'kill' ) );
			}

		} else {
			/**
			 * Pudło
			 */
			array_push ( $this->player->weaponFireResult, new weaponFireResult ( 'player', $this->player->userProperties->Name, $tTarget->userProperties->Name, $tWeapon->WeaponID, 'miss' ) );
			array_push ( $tTarget->weaponFireResult, new weaponFireResult ( 'target', $this->player->userProperties->Name, $tTarget->userProperties->Name, $tWeapon->WeaponID, 'miss' ) );

		}

		return true;
	}

	/**
	 * Obliczenie ilość EXP jaki gracz dostanie za wygraną walkę
	 * @param \Gameplay\Model\UserStatistics $myUserStats
	 * @param \Gameplay\Model\UserStatistics $enemyUserStats
	 * @return int
	 */
	static public function sComputeExperienceIncome(\Gameplay\Model\UserStatistics $myUserStats, \Gameplay\Model\UserStatistics $enemyUserStats) {

		/*
		 * Nowość: 30,000 za każdy level
		 */
		$retVal = 30000 * $enemyUserStats->Level;

		return $retVal;
	}

	/**
	 * Ilość exp jaką gracz traci za przegraną walkę
	 * @param \Gameplay\Model\UserStatistics $userStats
	 * @return int
	 */
	static public function sComputeExperienceLoss(\Gameplay\Model\UserStatistics $userStats) {

		$retVal = floor($userStats->Experience / 10);

		return $retVal;
	}

	/**
	 * Wystrzelenie z uzbrojenia okrętu
	 *
	 * @return boolean
	 */
	protected function fireWeapons() {

		global $config;

		/*
		 * Sprawdz, czy minął czas od strzelania
		 */
		if (time () - $this->player->userTimes->LastSalvo < $config ['combat'] ['salvoInterval']) {
			return false;
		}

		/*
		 * Przygotuj zapytanie zmienjszające amunicję
		 */
		$tPreparedUpdate = mysqli_prepare(\Database\Controller::getInstance()->getHandle(),"UPDATE shipweapons SET Ammo=Ammo-1 WHERE ShipWeaponID=?");

		/*
		 * Pobierz strzelające uzbrojenie okrętu
		 */
		$tQuery = $this->player->shipWeapons->get ( 'fireable' );
		while ( $tWeapon = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			/*
			 * Wylosuj okręt do którego strzelić
			 */
			$tEnemy = $this->getEnemy ();

			/*
			 * Nie ma nikogo do kogo można strzelic :)
			 */
			if (empty ( $tEnemy )) {
				break;
			}

			/*
			 * No to wystrzel z broni
			 */
			$this->fireWeapon ( $tWeapon, $tEnemy, $tPreparedUpdate );

		}

		/*
		 * Zapisz czas strzału
		 */
		$this->player->userTimes->LastSalvo = time ();

		return true;
	}

	/**
	 * Renderowanie boxa wroga
	 *
	 * @param combatShip $object
	 * @return string
	 */
	private function renderEnemyBox(combatShip $object) {

		$retVal = '';
		$retVal .= "<div class=\"shipPanel\">";
		$retVal .= "<table class=\"shipPanel\"><tr>";
		$retVal .= "<td class=\"shipPanel\" style=\"text-align: left;\"><div>";
		$retVal .= "<span style=\"font-size: 9pt; color: #f0f000; margin-right: 16px;\">{$object->userProperties->Name}</span>";
		$retVal .= "</div><div>";
		$retVal .= "<span style=\"font-size: 8pt; color: #f0f0f0; margin-right: 8px;\">{$object->shipProperties->SpecializationName}</span>";
		$retVal .= "<span style=\"font-size: 8pt; color: #f0f0f0; margin-right: 12px;\">{$object->shipProperties->ShipTypeName}</span>";
		$retVal .= "<span style=\"font-size: 8pt; color: #f0f000;\">{$object->shipProperties->OffRating}/{$object->shipProperties->DefRating}</span>";
		$retVal .= "</div>";
		$retVal .= "</td>";
		$retVal .= "<td class=\"shipPanel\">";

		/*
		 * Wyświetle jego statsy
		 */

		$tPercentage = \General\Formater::sGetPercentage ( $object->shipProperties->Shield, $object->shipProperties->ShieldMax );
		$tPercentage = '<span ' . getParameterColor ( $object->shipProperties->Shield, $object->shipProperties->ShieldMax ) . '> SHD: ' . $tPercentage . '%</span>';
		$retVal .= $tPercentage;
		$tPercentage = \General\Formater::sGetPercentage ( $object->shipProperties->Armor, $object->shipProperties->ArmorMax );
		$tPercentage = '<span ' . getParameterColor ( $object->shipProperties->Armor, $object->shipProperties->ArmorMax ) . '> ARM: ' . $tPercentage . '%</span>';
		$retVal .= $tPercentage;
		$tPercentage = \General\Formater::sGetPercentage ( $object->shipProperties->Power, $object->shipProperties->PowerMax );
		$tPercentage = '<span ' . getParameterColor ( $object->shipProperties->Power, $object->shipProperties->PowerMax ) . '> POW: ' . $tPercentage . '%</span>';
		$retVal .= $tPercentage;
		$tPercentage = \General\Formater::sGetPercentage ( $object->shipProperties->EmpMax - $object->shipProperties->Emp, $object->shipProperties->EmpMax );
		$tPercentage = '<span ' . getParameterColor ( $object->shipProperties->EmpMax - $object->shipProperties->Emp, $object->shipProperties->EmpMax ) . '> EMP: ' . $tPercentage . '%</span>';
		$retVal .= $tPercentage;

		$retVal .= "</td>";
		$retVal .= "</tr></table>";
		$retVal .= "</div>";

		return $retVal;
	}

	/**
	 * Pobranie dotyczących mnie raportów z bazy
	 *
	 * @return mixed
	 */
	protected function getMyCombatMessages() {
		$retVal = '';

		$tQuery = "SELECT * FROM combatmessages WHERE UserID='{$this->userID}' AND Displayed='no' AND Type='defensive' ORDER BY CreateTime DESC";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$tObject = weaponFireResult::sDecodeSaveModel ( $tResult->Text );
			$retVal .= $tObject->render ( $this->t, $this->Language );
		}

		$tQuery = "UPDATE combatmessages SET Displayed='yes' WHERE UserID='{$this->userID}' AND Displayed='no'";
		\Database\Controller::getInstance()->executeAndRetryOnDeadlock ( $tQuery );

		return $retVal;
	}

	/**
	 * Statki w sektorze które nie biorą udziału w walce
	 *
	 * @return string
	 */
	protected function renderOtherShips() {

		$retVal = '';

		$nameField = "Name" . $this->Language;

		$tQuery = "SELECT
            userships.UserID AS UserID,
            userships.RookieTurns AS RookieTurns,
            users.Name AS PlayerName,
            users.Type AS UserType,
            userstats.Level AS Level,
            specializations.$nameField AS SpecializationName,
            shiptypes.$nameField AS ShipTypeName,
            userships.OffRating AS OffRating,
            userships.DefRating AS DefRating,
            userships.Cloak AS Cloak,
            alliances.Name As AllianceName,
            alliances.AllianceID
          FROM
            shippositions JOIN userships USING(UserID)
            JOIN shiptypes ON shiptypes.ShipID = userships.ShipID
            JOIN users ON users.UserID = shippositions.UserID
            JOIN userstats ON userstats.UserID=shippositions.UserID
            LEFT JOIN specializations ON specializations.SpecializationID = userships.SpecializationID
            LEFT JOIN alliancemembers ON alliancemembers.UserID=shippositions.UserID
            LEFT JOIN alliances ON alliances.AllianceID = alliancemembers.AllianceID
          WHERE
            shippositions.System='{$this->shipPosition->System}' AND
            shippositions.X='{$this->shipPosition->X}' AND
            shippositions.Y='{$this->shipPosition->Y}' AND
            shippositions.Docked='no' AND
            shippositions.UserID != '{$this->userID}' AND
            (SELECT COUNT(*) FROM combatlock AS cl WHERE cl.UserID=userships.UserID AND cl.ByUserID='{$this->userID}') = 0
          ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );

		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			if ($this->shipPosition->Docked == 'no' && ! \Gameplay\Model\ShipProperties::sGetVisibility ( $this->player->shipProperties, $this->player->userStats, $tR1, $tR1, $this->sectorProperties )) {
				continue;
			}

			if ($tR1->AllianceName == null) {
			    $tR1->AllianceName = $this->t->get ( 'noalliance' );
            }

			$retVal .= "<div class=\"shipPanel\">";
			$retVal .= "<table class=\"shipPanel\"><tr>";
			$retVal .= "<td class=\"shipPanel\" style=\"width: 110px;\">{$tR1->AllianceName}</td>";
			$retVal .= "<td class=\"shipPanel\" style=\"text-align: left;\"><div>";
			$retVal .= "<span style=\"font-size: 9pt; color: #f0f000; margin-right: 16px;\">{$tR1->PlayerName}</span>";
			$retVal .= "<span style=\"font-size: 8pt; color: #00f000;\">" . $this->t->get ( 'level' ) . ": {$tR1->Level}</span>";
			$retVal .= "</div><div>";
			$retVal .= "<span style=\"font-size: 8pt; color: #f0f0f0; margin-right: 8px;\">{$tR1->SpecializationName}</span>";
			$retVal .= "<span style=\"font-size: 8pt; color: #f0f0f0; margin-right: 12px;\">{$tR1->ShipTypeName}</span>";
			$retVal .= "<span style=\"font-size: 8pt; color: #f0f000;\">{$tR1->OffRating}/{$tR1->DefRating}</span>";
			$retVal .= "</div>";
			$retVal .= "</td>";
			$retVal .= "<td class=\"shipPanel\" style=\"width: 60px;\">";
			if ($tR1->RookieTurns < 1 && $this->player->shipProperties->RookieTurns < 1  && ($tR1->AllianceID != $this->playerAlliance || empty($this->playerAlliance))) {
				$retVal .= "<div class=\"attackButton\" onclick=\"Playpulsar.gameplay.execute('shipAttack',null,null,{$tR1->UserID},null);\">" . $this->t->get ( 'attack' ) . "</div>";
			}
			$retVal .= "</td>";
			$retVal .= "</tr></table>";
			$retVal .= "</div>";

		}

		return $retVal;

	}

	protected function render() {

		global $config;

		$template = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/combat.html', $this->t);

		$tPercentage = \General\Formater::sGetPercentage ( $this->player->shipProperties->Shield, $this->player->shipProperties->ShieldMax );
		$tPercentage = '<span ' . getParameterColor ( $this->player->shipProperties->Shield, $this->player->shipProperties->ShieldMax ) . '>' . $tPercentage . '%</span>';
		$template->add ( 'ShieldValue', $tPercentage );
		$tPercentage = \General\Formater::sGetPercentage ( $this->player->shipProperties->Armor, $this->player->shipProperties->ArmorMax );
		$tPercentage = '<span ' . getParameterColor ( $this->player->shipProperties->Armor, $this->player->shipProperties->ArmorMax ) . '>' . $tPercentage . '%</span>';
		$template->add ( 'ArmorValue', $tPercentage );
		$tPercentage = \General\Formater::sGetPercentage ( $this->player->shipProperties->Power, $this->player->shipProperties->PowerMax );
		$tPercentage = '<span ' . getParameterColor ( $this->player->shipProperties->Power, $this->player->shipProperties->PowerMax ) . '>' . $tPercentage . '%</span>';
		$template->add ( 'PowerValue', $tPercentage );
		$tPercentage = \General\Formater::sGetPercentage ( $this->player->shipProperties->EmpMax - $this->player->shipProperties->Emp, $this->player->shipProperties->EmpMax );
		$tPercentage = '<span ' . getParameterColor ( $this->player->shipProperties->EmpMax - $this->player->shipProperties->Emp, $this->player->shipProperties->EmpMax ) . '>' . $tPercentage . '%</span>';
		$template->add ( 'EmpValue', $tPercentage );

		$tString = '';
		foreach ( $this->enemies as $tEnemy ) {
			$tString .= $this->renderEnemyBox ( $tEnemy );
		}

		$template->add ( 'EnemiesList', $tString );
		$template->add ( 'OtherShipsList', $this->renderOtherShips () );

		$tString = '';
		foreach ( $this->player->weaponFireResult as $tResult ) {
			$tString .= $tResult->render($this->t, $this->Language);
		}
		$template->add ( 'yourCombatReports', $tString );

		$template->add ( 'otherCombatReports', $this->getMyCombatMessages () );

		$weaponsRegistry = new combatShipWeaponsRegistry ( $this->userID );
		$template->add ( 'YourWeaponsList', $weaponsRegistry->get ( $this->player->shipWeapons, $this->t ) );

		$nextSalvo = $this->player->userTimes->LastSalvo + $config ['combat'] ['salvoInterval'] - time ();
		if ($nextSalvo < 0) {
			$nextSalvo = 0;
		}
		$template->add ( 'salvoInterval', $nextSalvo );

		/*
		 * Sprawdz, czy jest ktokolwiek z kim można jeszcze walczyć
		 */
		if ($this->getUsableEnemiesCount () > 0) {
			$template->remove('closeButton');
		} else {
			$template->remove('combatButtons');
		}

		$this->retVal .= ( string ) $template;
	}

	/**
	 * Pobierz liczbę statków z którmi mozna walczyć
	 *
	 * @return int
	 */
	protected function getUsableEnemiesCount() {
		$retVal = 0;

		foreach ( $this->enemies as $tEnemy ) {
			if ($tEnemy->shipProperties->Armor > 0) {
				$retVal ++;
			}
		}

		return $retVal;
	}

	public function execute($action, $options = null) {

		global $config;

        $oDb = Database\Controller::getInstance();

		/**
		 * @since 2011-06-01
		 * Jeśli strzela NPC, daj sobie spokój z rederowaniem outputu
		 */
		if (!isset($options['renderResult'])) {
			$options['renderResult'] = true;
		}

		if (!isset($options['doSummon'])) {
			$options['doSummon'] = true;
		}

		self::sRefreshCombatLock ( $this->userID );

		if ($this->player->shipProperties->RookieTurns > 0) {

			/**
			 * Znak, że jestes zniszczony
			 */
			$this->renderDestroyed ();
		} else {

			switch ($action) {

				case 'disengage' :

					$this->loadEnemies();

					if (time () - $this->player->userTimes->LastSalvo < $config ['combat'] ['salvoInterval']) {
						return false;
					}

					$this->player->userTimes->LastSalvo = time ();

					$this->player->setLastAction('disengage');

					if (self::sCheckDisengage ( $this->player, $this->enemies, $this->sectorProperties )) {

						/**
						 * Rozłącz walkę
						 */
						self::sDisengage ( $this->userID );

						/*
						 * Zapisz info o disengage
						 */
						foreach ( $this->enemies as $tEnemy ) {
							array_push ( $tEnemy->weaponFireResult, new weaponFireResult ( 'target', $this->player->userProperties->Name, $tEnemy->userProperties->Name, null, 'disengage' ) );
						}

						$this->renderDisengage ();

					} else {
						if (!empty($options['renderResult'])) {
							$this->render ();
						}
					}

					break;

				case 'fireWeapons' :


					$this->loadEnemies();

					/**
					 * Summonowanie NPC
					 */
					if (!empty($options['doSummon']) && isset($this->enemies[0])) {
						static::sProtectiveNpcController($this->shipPosition, $this->enemies[0]->userID);
					}

					$this->fireWeapons ();

					if (!empty($options['renderResult'])) {
						$this->render ();
					}

					break;

				case 'mayday' :
					new newsAgencyMessage ( 1, $this->player->userProperties, null, $this->shipPosition );
					$this->player->userTimes->LastSalvo = time ();
					if (!empty($options['renderResult'])) {
						$this->loadEnemies();
						$this->render ();
					}
					break;

				default :
				case 'refresh' :
					if (!empty($options['renderResult'])) {
						$this->loadEnemies();
						$this->render ();
					}
					break;
			}

			/*
			 * Zapisz raport dla innych graczy w bazie danch
			 */

			/*
			 * Rozpocznij transakcję
			 */
            $oDb->disableAutocommit();

			$sPreparedInsert = mysqli_prepare($oDb->getHandle(), "INSERT INTO combatmessages(CreateTime, UserID, ByUserID, Text, Type) VALUES(?,?,?,?,?)");

			foreach ( $this->enemies as $tShip ) {

				/*
				 * Dla NPC nie zapisuj fireResult
				 */
				if ($tShip->userProperties->Type == 'npc') {
					continue;
				}

				foreach($tShip->weaponFireResult as $tResult ) {
					$tResult->save($tShip->userID, $this->userID, 'defensive', $sPreparedInsert );
				}
			}
			/*
			 * Zapisz moje raporty w bazie danych
			 */
			foreach ( $this->player->weaponFireResult as $tResult ) {
				$tResult->save($this->userID, $this->userID, 'offensive', $sPreparedInsert);
			}
			/*
			 * Skomituj transakcję
			 */
            $oDb->commit();
            $oDb->enableAutocommit();
		}

		//FIXME move authcode outside combat class
		\Gameplay\Model\UserTimes::genAuthCode ( $this->player->userTimes, $this->player->userFastTimes );
		$this->authCode = $this->player->userFastTimes->AuthCode;
        return true;
	}

	/**
	 * Screen rozłączenia
	 *
	 */
	protected function renderDisengage() {
		$template = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/disengage.html', $this->t );
		$this->retVal .= ( string ) $template;
	}

	protected function renderDestroyed() {
		$template = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/destroyed.html', $this->t );
		$template->add ( 'otherCombatReports', $this->getMyCombatMessages () );
		$this->retVal .= ( string ) $template;
	}

	/**
	 * Średnie manu przeciwników
	 * @param array $tEnemies
	 * @return int
	 */
	static protected function sGetAverageManeuver($tEnemies) {

		$retVal = 0;

		$tEnemyCount = 0;

		foreach ( $tEnemies as $tEnemy ) {
			$retVal += $tEnemy->shipProperties->Maneuver;
			$tEnemyCount ++;
		}

		if ($tEnemyCount == 0) {
			$tEnemyCount = 1;
		}

		$retVal = $retVal / $tEnemyCount;

		return $retVal;
	}

	/**
	 * Pobranie średnie szybkości przeciwnikow
	 *
	 * @param array $tEnemies
	 * @return int
	 */
	static protected function sGetAverageSpeed($tEnemies) {

		$retVal = 0;

		$tEnemyCount = 0;

		foreach ( $tEnemies as $tEnemy ) {
			$retVal += $tEnemy->shipProperties->Speed;
			$tEnemyCount ++;
		}

		if ($tEnemyCount == 0) {
			$tEnemyCount = 1;
		}

		$retVal = $retVal / $tEnemyCount;

		return $retVal;
	}

	/**
	 * Pobranie średniego Lvl przeciwników
	 *
	 * @param array $tEnemies
	 * @return float
	 */
	static protected function sGetAverageLevel($tEnemies) {

		$retVal = 0;

		$tEnemyCount = 0;

		foreach ( $tEnemies as $tEnemy ) {
			$retVal += $tEnemy->userStats->Level;
			$tEnemyCount ++;
		}

		if ($tEnemyCount == 0) {
			$tEnemyCount = 1;
		}

		$retVal = $retVal / $tEnemyCount;

		return $retVal;
	}

	/**
	 * Czy nastąpi rozłącznie walki
	 *
	 * @param combatShip $tPlayer
	 * @param array $tEnemies
	 * @param \Gameplay\Model\SectorEntity $sectorProperties
	 * @return boolean
	 */
	static public function sCheckDisengage($tPlayer, $tEnemies, \Gameplay\Model\SectorEntity $sectorProperties) {

		$avgSpeed = self::sGetAverageSpeed ( $tEnemies );
		$avgManu = self::sGetAverageManeuver( $tEnemies );

		//@todo to równanie jednak do zmiany. Trzeba uwzględnić liczbę przeciwników

		$percentage = floor(20 + (($tPlayer->shipProperties->Speed - $avgSpeed) * 2) + ((100 - $sectorProperties->Visibility) / 5) + (($tPlayer->shipProperties->Maneuver - $avgManu) / 100));

		if ($percentage > 50) {
			$percentage = 50;
		}

		if ($percentage < 10) {
			$percentage = 10;
		}

		if (additional::checkRand ( $percentage, 100 )) {
			$retVal = true;
		} else {
			$retVal = false;
		}

		return $retVal;
	}

	/**
	 * @param int $userID
	 * @param string $Language
	 */
	public function __construct($userID, $Language) {

		$this->userID = $userID;
		$this->Language = $Language;

		$this->t = new Translate( $this->Language, dirname ( __FILE__ ) . '/../translations.php' );

		$this->weaponsFireResults = array ();

		$this->shipPosition = new \Gameplay\Model\ShipPosition($this->userID);

		$this->player = new combatShip ( $userID, $Language, clone $this->shipPosition );

		$tAllianceObject = new userAlliance ( );
		$this->playerAlliance = $tAllianceObject->load ( $userID, true, true )->AllianceID;

		$this->sectorProperties = new \Gameplay\Model\SectorEntity($this->shipPosition);
	}

	/**
	 * Załadowanie listy wrogów z którymi jest walka
	 */
	private function loadEnemies() {

		$this->enemies = array ();
		$tQuery = "SELECT ByUserID FROM combatlock WHERE UserID='{$this->userID}' AND Active='yes'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			array_push ( $this->enemies, new combatShip ( $tResult->ByUserID, $this->Language ) );
		}
	}

	/**
	 * Odświerzenie combat lock
	 *
	 * @param int $userID
	 * @return boolean
	 */
	static private function sRefreshCombatLock($userID) {

		$tQuery = "UPDATE combatlock SET LastTime=" . time () . " WHERE (UserID='{$userID}' OR ByUserID='{$userID}') AND Active='yes'";
		\Database\Controller::getInstance()->execute ( $tQuery );

		return true;
	}

	/**
	 * Sprawdzenie, czy okręt znajduje się w walce
	 *
	 * @param int $userID
	 * @return boolean
	 */
	static public function sCheckCombatLock($userID) {

		$retVal = false;

		$tQuery = "SELECT COUNT(*) AS ILE FROM combatlock WHERE UserID='{$userID}' AND Active='yes'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		if (\Database\Controller::getInstance()->fetch ( $tQuery )->ILE != 0) {
			$retVal = true;
		}

		return $retVal;
	}

	/**
	 * Garbage collector tabeli combatlock
	 *
	 */
	static public function sCombatLockGarbageCollection() {

		global $config;

		$tQuery = "DELETE FROM combatlock WHERE LastTime<'" . (time () - $config ['combat'] ['autoDisengageThreshold']) . "'";
		\Database\Controller::getInstance()->execute ( $tQuery );

	}

	/**
	 * Rozłącznie z walki
	 *
	 * @param int $userID
	 */
	static public function sDisengage($userID) {

		\Database\Controller::getInstance()->execute ( "UPDATE combatlock SET Active='no' WHERE UserID='$userID'" );
		\Database\Controller::getInstance()->execute ( "UPDATE combatlock SET Active='no' WHERE ByUserID='$userID'" );

	}

	/**
	 * Założenie combat lock, operuje na poziomie bazy
	 *
	 * @param int $attackedID
	 * @param int $defenderID
	 * @return boolean
	 */
	static private function sInsertCombatLock($attackedID, $defenderID) {

		/*
		 * Sprawdz, pobierz wpisy dotyczące tej pary
		 */

		$goInsert = true;
		$goUpdate = false;

		$tQuery = "SELECT * FROM combatlock WHERE UserID='{$defenderID}' AND ByUserID='{$attackedID}'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$goInsert = false;

			if ($tResult->Active == 'yes') {
				$goUpdate = true;
			}
		}

		if ($goUpdate) {
			\Database\Controller::getInstance()->execute ( "UPDATE combatlock SET LastTime='" . time () . "' WHERE UserID='{$defenderID}' AND ByUserID='{$attackedID}'" );
		}

		if ($goInsert) {
			\Database\Controller::getInstance()->execute ( "INSERT INTO combatlock(UserID, ByUserID, LastTime,Active) VALUES('{$defenderID}','{$attackedID}','" . time () . "','yes')" );
		}

		return true;
	}

    /**
     * @param $attackerID
     * @param $defenderID
     * @param bool $enablePositionCheck
     * @return bool
     * @throws securityException
     */
    static public function sSetCombatLock($attackerID, $defenderID, $enablePositionCheck = true) {

		/*
		 * Pobierz parametry
		 */
        $attackerProperties = new \Gameplay\Model\ShipProperties($attackerID);
        $defenderProperties = new \Gameplay\Model\ShipProperties($defenderID);

		$attackerPosition = new \Gameplay\Model\ShipPosition($attackerID);
		$defenderPosition = new \Gameplay\Model\ShipPosition($defenderID);

		$attackerAllianceObject = new userAlliance ( );
		$attackerAlliance = $attackerAllianceObject->load ( $attackerID, true, true );

		$defenderAllianceObject = new userAlliance ( );
		$defenderAlliance = $defenderAllianceObject->load ( $defenderID, true, true );

		/*
		 * Sprawdz, czy można atakować
		 */

		if ($attackerProperties->RookieTurns > 0 || $defenderProperties->RookieTurns > 0) {
			throw new securityException ( TranslateController::getDefault()->get('Rookie protected') );
		}

		if ($attackerProperties->checkMalfunction()) {
			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'error', '{T:shipMalfunctionEmp}');
			return false;
		}


		if ($enablePositionCheck) {
			if ($attackerPosition->System != $defenderPosition->System) {
				throw new securityException ( TranslateController::getDefault()->get('Player has left this sector') );
			}
			if ($attackerPosition->X != $defenderPosition->X) {
				throw new securityException ( TranslateController::getDefault()->get('Player has left this sector') );
			}
			if ($attackerPosition->Y != $defenderPosition->Y) {
				throw new securityException ( TranslateController::getDefault()->get('Player has left this sector') );
			}

			if ($attackerPosition->Docked == 'yes' || $defenderPosition->Docked == 'yes') {
				throw new securityException ( TranslateController::getDefault()->get('Player has left this sector') );
			}
		}

		$tAttackers = array ();
		$tDefenders = array ();

		/*
		 * Pobierz statki do założenia combat lock
		 */
		if (empty ( $attackerAlliance->AllianceID ) && empty ( $defenderAlliance->AllianceID )) {
			/*
			 * Oba statki bez sojuszu
			 */
			array_push ( $tAttackers, $attackerID );
			array_push ( $tDefenders, $defenderID );

		} elseif (empty ( $attackerAlliance->AllianceID ) && ! empty ( $defenderAlliance->AllianceID )) {
			/*
			 * Atakowany z sojuszu
			 */
			array_push ( $tAttackers, $attackerID );

			/*
			 * Pobierz wszystkie statki z tego sojuszu w tym sektorze
			 */
			$tQuery = "SELECT
			     sp.UserID
			   FROM
			     shippositions AS sp JOIN userships AS us USING(UserID) LEFT JOIN alliancemembers AS am Using(UserID)
			   WHERE
			     sp.System='{$attackerPosition->System}' AND
			     sp.X = '{$attackerPosition->X}' AND
			     sp.Y = '{$attackerPosition->Y}' AND
			     sp.Docked = 'no' AND
			     us.RookieTurns = '0' AND
			     am.AllianceID = '{$defenderAlliance->AllianceID}' AND
			     us.Squadron = '{$defenderProperties->Squadron}'
			   ";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				array_push ( $tDefenders, $tResult->UserID );
			}

		} elseif (! empty ( $attackerAlliance->AllianceID ) && empty ( $defenderAlliance->AllianceID )) {
			/*
			 * Atakujący z sojuszu
			 */
			array_push ( $tDefenders, $defenderID );

			/*
			 * Pobierz wszystkie statki z tego sojuszu w tym sektorze
			 */
			$tQuery = "SELECT
           sp.UserID
         FROM
           shippositions AS sp JOIN userships AS us USING(UserID) LEFT JOIN alliancemembers AS am Using(UserID)
         WHERE
           sp.System='{$attackerPosition->System}' AND
           sp.X = '{$attackerPosition->X}' AND
           sp.Y = '{$attackerPosition->Y}' AND
           sp.Docked = 'no' AND
           us.RookieTurns = '0' AND
           am.AllianceID = '{$attackerAlliance->AllianceID}' AND
           us.Squadron = '{$attackerProperties->Squadron}'
         ";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				array_push ( $tAttackers, $tResult->UserID );
			}

		} else {
			/*
			 * Oba statki z sojuszu
			 */

			$tQuery = "SELECT
           sp.UserID
         FROM
           shippositions AS sp JOIN userships AS us USING(UserID) LEFT JOIN alliancemembers AS am Using(UserID)
         WHERE
           sp.System='{$attackerPosition->System}' AND
           sp.X = '{$attackerPosition->X}' AND
           sp.Y = '{$attackerPosition->Y}' AND
           sp.Docked = 'no' AND
           us.RookieTurns = '0' AND
           am.AllianceID = '{$attackerAlliance->AllianceID}' AND
           us.Squadron = '{$attackerProperties->Squadron}'
         ";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				array_push ( $tAttackers, $tResult->UserID );
			}

			$tQuery = "SELECT
           sp.UserID
         FROM
           shippositions AS sp JOIN userships AS us USING(UserID) LEFT JOIN alliancemembers AS am Using(UserID)
         WHERE
           sp.System='{$attackerPosition->System}' AND
           sp.X = '{$attackerPosition->X}' AND
           sp.Y = '{$attackerPosition->Y}' AND
           sp.Docked = 'no' AND
           us.RookieTurns = '0' AND
           am.AllianceID = '{$defenderAlliance->AllianceID}' AND
           us.Squadron = '{$defenderProperties->Squadron}'
         ";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				array_push ( $tDefenders, $tResult->UserID );
			}

		}

		/*
		 * Na podstawie tablicy zainicjuj combatlock
		 */
		foreach ( $tAttackers as $tAttacker ) {
			foreach ( $tDefenders as $tDefender ) {
				self::sInsertCombatLock ( $tAttacker, $tDefender );
			}
		}
		foreach ( $tDefenders as $tDefender ) {
			foreach ( $tAttackers as $tAttacker ) {
				self::sInsertCombatLock ( $tDefender, $tAttacker );
			}
		}

		return true;
	}

	/**
	 * Czy ma zostać uszkodzony equipment
	 *
	 * @param stdClass $tWeapon
	 * @param combatShip $firingShip
	 * @param combatShip $targetShip
	 * @return boolean
	 */
	protected static function checkEquipmentDamage($tWeapon, combatShip $firingShip, combatShip $targetShip) {
		global $config;
		return additional::checkRand ( $config ['combat'] ['equipmentDamageProbability'], 100 );
	}

	/**
	 * Czy ma zostać uszkodzone uzbrojenie
	 *
	 * @param stdClass $tWeapon
	 * @param combatShip $firingShip
	 * @param combatShip $targetShip
	 * @return boolean
	 */
	protected static function checkWeaponsDamage($tWeapon, combatShip $firingShip, combatShip $targetShip) {
		global $config;
		return additional::checkRand ( $config ['combat'] ['weaponDamageProbability'], 100 );
	}

    /**
     * @param stdClass $tWeapon
     * @param combatShip $firingShip
     * @param combatShip $targetShip
     * @param \Gameplay\Model\SectorEntity $sectorProperties
     * @return int
     */
    protected static function computeWeaponAccuracy($tWeapon, combatShip $firingShip, combatShip $targetShip, \Gameplay\Model\SectorEntity $sectorProperties) {

		$iManeuver = $targetShip->shipProperties->Maneuver;

		if ($targetShip->getLastAction() == 'disengage') {
			$iManeuver = $iManeuver * 2;
		}

		$tSize = $targetShip->shipSize;

		$tDivider = 11-$tSize;

		$iManeuver = ceil(($iManeuver / 10)*$tDivider);

		$retVal = $tWeapon->Accuracy + ($firingShip->shipProperties->Targetting * 10) + ($firingShip->userStats->Level - $targetShip->userStats->Level) - ($iManeuver / 10) + $sectorProperties->Accuracy - 100;

		if ($retVal < 5) {
			$retVal = 5;
		}

		if ($retVal > 95) {
			$retVal = 95;
		}
		return $retVal;
	}

	/**
	 * Sprawdzenie warunku penetracji osłony
	 *
	 * @return boolean
	 */
	protected static function checkShieldPenetration() {

		global $config;

		return additional::checkRand ( $config ['combat'] ['shdPenetrationProbability'], 100 );
	}

	/**
	 * Obliczenie mocy uderzenia w pancerz w przypadku przebicia osłony
	 *
	 * @param stdClass $weaponData
	 * @return int
	 */
	protected static function computePenetrationDamage($weaponData) {

		$retVal = additional::rand ( $weaponData->ArmorMin, $weaponData->ArmorMax ) / 2;

		return floor ( $retVal );
	}

	/**
	 * Zwiększenie mocy broni o wartość procentową
	 *
	 * @param int $value
	 * @param int $multiplier [%]
	 */
	protected static function weaponDamageUpper(&$value, $multiplier) {
		if ($multiplier != 0) {
			$value += $value / 100 * $multiplier;
		}
	}

	/**
	 * Zmniejszenie mocy broni o wartość procentową
	 *
	 * @param int $value
	 * @param int $multiplier [%]
	 */
	protected static function weaponDamageDowner(&$value, $multiplier) {
		if ($multiplier != 0) {
			$value -= $value / 100 * $multiplier;
		}
	}

	/**
	 * Zwiększenie obrażeń przez uderzenie krytyczne
	 *
	 * @param int $value
	 * @param stdClass $weaponData
	 */
	protected static function weaponDamageMultiplier(&$value, $weaponData) {
		if (additional::checkRand ( $weaponData->CriticalProbability, 100 )) {
			$value *= $weaponData->CriticalMultiplier;
			self::$weaponCriticalHit = true;
		}
	}

	/**
	 * Obliczenie mocy uderzenie broni w osłonę
	 *
	 * @param stdClass $weaponData - dane broni
	 * @param combatShip $firingShip - parametry statku strzelającego
	 * @param combatShip $firedShip - parametry statku strzelanego
	 * @return int
	 */
	protected static function computeShieldDamage($weaponData, $firingShip, $firedShip) {

		$retVal = additional::rand ( $weaponData->ShieldMin, $weaponData->ShieldMax );

		if ($retVal == 0) {
		    return 0;
        }

		/*
		 * Zwiększenie mocy przez upgady statku strzelającego
		 */
		//		self::weaponDamageUpper ( $retVal, $firingShip->Modifiers->Weapon->Damage->Add->ByClass [$weaponData->WeaponClassID] );

		/*
		 * Zmniejszenie mocy przez upgrady statu w który oddawany jest strzal
		 */
		//self::weaponDamageDowner ( $retVal, $firedShip->Modifiers->Weapon->Damage->Sub->ByClass [$weaponData->WeaponClassID] );

		/*
		 * Szansa na uszkodzenia krytyczne
		 */
		self::weaponDamageMultiplier ( $retVal, $weaponData );

		return floor ( $retVal );
	}

	/**
	 * Negacja obrażeń pancerza przez ArmorStrength
	 *
	 * @param float $damage
	 * @param combatShip $firingShip
	 * @param combatShip $targetShip
	 * @return float
	 */
	protected static function applyArmorStrength($damage, $firingShip, $targetShip) {

		$tPercent = $targetShip->shipProperties->ArmorStrength - ($firingShip->shipProperties->ArmorPiercing * 8) - 8;

		/*
		 * Capping
		 */
		if ($tPercent < 0) {
			$tPercent = 0;
		}
		if ($tPercent > 90) {
			$tPercent = 90;
		}

		$tPercent = 1 - ($tPercent / 100);
		$damage = $damage * $tPercent;

		return $damage;
	}

	/**
	 * Obliczenie mocy uderzenie broni w pancerz
	 *
	 * @param stdClass $weaponData - dane broni
	 * @param combatShip $firingShip - parametry statku strzelającego
	 * @param combatShip $firedShip - parametry statku strzelanego
	 * @return int
	 */
	protected static function computeArmorDamage($weaponData, $firingShip, $firedShip) {
		$retVal = additional::rand($weaponData->ArmorMin, $weaponData->ArmorMax);

		if ($retVal == 0) {
		    return 0;
        }

		/*
		 * Zwiększenie mocy przez upgady statku strzelającego
		 */
		//		self::weaponDamageUpper ( $retVal, $firingShip->Modifiers->Weapon->Damage->Add->ByClass [$weaponData->WeaponClassID] );


		/*
		 * Zmniejszenie mocy przez upgrady statu w który oddawany jest strzal
		 */
		//		self::weaponDamageDowner ( $retVal, $firedShip->Modifiers->Weapon->Damage->Sub->ByClass [$weaponData->WeaponClassID] );

		/*
		 * Szansa na uszkodzenia krytyczne
		 */
		self::weaponDamageMultiplier ( $retVal, $weaponData );

		/*
		 * Zastosuj ArmorStrength
		 */
		$retVal = self::applyArmorStrength ( $retVal, $firingShip, $firedShip );

		return floor($retVal);
	}

	/**
	 * Obliczenie mocy Power Drain
	 *
	 * @param stdClass $weaponData - dane broni
	 * @param combatShip $firingShip - parametry statku strzelającego
	 * @param combatShip $firedShip - parametry statku strzelanego
	 * @return int
	 */
	protected static function computePowerDamage($weaponData, $firingShip, $firedShip) {
		$retVal = additional::rand ( $weaponData->PowerMin, $weaponData->PowerMax );

		if ($retVal == 0)
		return 0;

		/*
		 * Zwiększenie mocy przez upgady statku strzelającego
		 */
		//		self::weaponDamageUpper ( $retVal, $firingShip->Modifiers->Weapon->Damage->Add->ByClass [$weaponData->WeaponClassID] );


		/*
		 * Zmniejszenie mocy przez upgrady statu w który oddawany jest strzal
		 */
		//		self::weaponDamageDowner ( $retVal, $firedShip->Modifiers->Weapon->Damage->Sub->ByClass [$weaponData->WeaponClassID] );


		/*
		 * Szansa na uszkodzenia krytyczne
		 */
		self::weaponDamageMultiplier ( $retVal, $weaponData );

		return floor ( $retVal );
	}

	/**
	 * Obliczenie mocy uszkodzeń EMP
	 *
	 * @param stdClass $weaponData - dane broni
	 * @param combatShip $firingShip - parametry statku strzelającego
	 * @param combatShip $firedShip - parametry statku strzelanego
	 * @return int
	 */
	protected static function computeEmpDamage($weaponData, $firingShip, $firedShip) {
		$retVal = additional::rand ( $weaponData->EmpMin, $weaponData->EmpMax );

		if ($retVal == 0)
		return 0;

		/*
		 * Zwiększenie mocy przez upgady statku strzelającego
		 */
		//		self::weaponDamageUpper ( $retVal, $firingShip->Modifiers->Weapon->Damage->Add->ByClass [$weaponData->WeaponClassID] );

		/*
		 * Zmniejszenie mocy przez upgrady statu w który oddawany jest strzal
		 */
		//		self::weaponDamageDowner ( $retVal, $firedShip->Modifiers->Weapon->Damage->Sub->ByClass [$weaponData->WeaponClassID] );

		/*
		 * Szansa na uszkodzenia krytyczne
		 */
		self::weaponDamageMultiplier ( $retVal, $weaponData );

		return floor ( $retVal );
	}

    /**
     * Protective NPC summon controller
     * @param \Gameplay\Model\ShipPosition $position
     * @param int $defenderID
     * @param bool $initProcedure
     * @return bool
     */
    static public function sProtectiveNpcController(\Gameplay\Model\ShipPosition $position, $defenderID, $initProcedure = false) {

		$tAllianceObject = new userAlliance ( );
		$defenderAlliance = $tAllianceObject->load ( $defenderID, true, true )->AllianceID;
		unset($tAllianceObject);

		$allianceData = new \Gameplay\Model\Alliance($defenderAlliance);
		if (empty($allianceData->Defendable)) {
			$allianceData->Defendable = 'yes';
		}
		if ($allianceData->Defendable == 'no') {
			return false;
		}

		$positionHash = md5($position->System.'|'.$position->X.'|'.$position->Y);

		/*
		 * Utwórz wpis w sesji
		 */
		if (empty($_SESSION['npcSummoner'][$positionHash])) {
			$currentStatus = 0;
		}else {
			$currentStatus = $_SESSION['npcSummoner'][$positionHash];
		}

		if ($currentStatus == 0 && $initProcedure == false) {
			return false;
		}

		$currentStatus++;

		switch ($currentStatus) {

			case 2:
				npc::sSummonProtective($position, $defenderAlliance);
				break;

			case 3:
				$tArray = npc::sGetProtectiveAtPosition($position, $defenderAlliance);
				foreach ($tArray as $npcID) {
					self::sInsertCombatLock($_SESSION['userID'], $npcID);
					self::sInsertCombatLock($npcID, $_SESSION['userID']);
				}

				break;
		}

		$_SESSION['npcSummoner'][$positionHash] = $currentStatus;

		/*
		 * GarbageCollector
		 */
		self::sNpcSummonGarbageCollector($positionHash);
        return true;
	}

    /**
     * @param $positionHash
     * @return bool
     */
    static private function sNpcSummonGarbageCollector($positionHash) {

		if (rand(1,10) == 5) {

			foreach ($_SESSION['npcSummoner'] as $tKey => $tValue) {
				if ($tKey != $positionHash) {
					unset($_SESSION['npcSummoner'][$tKey]);
				}
			}

		}

		return true;
	}

}

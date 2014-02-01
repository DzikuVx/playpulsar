<?php

class combatShip {

    /**
     * @var weaponFireResult[]
     */
    public $weaponFireResult = null;

    /**
     * @var string
     */
    public $Language = 'en';

    /**
     * @var int
     */
    public $userID = null;

    /**
     * @var \Gameplay\Model\ShipProperties
     */
    public $shipProperties = null;

	/**
	 * @var \Gameplay\Model\ShipWeapons
	 */
	public $shipWeapons = null;

    /**
     * @var \Gameplay\Model\ShipEquipments
     */
    public $shipEquipment = null;

	/**
	 * Ładownia
	 *
	 * @var shipcargo
	 */
	public $shipCargo = null;

	/**
	 * @var \Gameplay\Model\UserTimes
	 */
	public $userTimes = null;

	/**
	 * @var \Gameplay\Model\UserFastTimes
	 */
	public $userFastTimes = null;

    /**
     * @var \Gameplay\Model\UserEntity
     */
    public $userProperties = null;

    /**
     * @var \Gameplay\Model\UserStatistics
     */
    public $userStats = null;

	/**
	 * @var \Gameplay\Model\ShipPosition
	 */
	public $shipPosition = null;

    /**
     * @var int
     */
    public $shipSize = 1;

    /**
     * @param int $userID
     * @param string $Language
     * @param \Gameplay\Model\ShipPosition $shipPosition
     */
    public function __construct($userID, $Language, \Gameplay\Model\ShipPosition $shipPosition = null) {
		$this->userID = $userID;
		$this->Language = $Language;
		$this->weaponFireResult = array();

        $this->shipProperties = new \Gameplay\Model\ShipProperties($userID);

		$this->userTimes     = new \Gameplay\Model\UserTimes($userID);
		$this->userFastTimes = new \Gameplay\Model\UserFastTimes($userID);
        $this->userProperties = new \Gameplay\Model\UserEntity($userID);
        $this->userStats = new \Gameplay\Model\UserStatistics($userID);

		$this->shipWeapons = new \Gameplay\Model\ShipWeapons($this->userID, $this->Language);
		$this->shipEquipment = new \Gameplay\Model\ShipEquipments($this->userID, $this->Language);
		$this->shipCargo = new shipCargo($this->userID, $this->Language);

		if (!empty ($shipPosition)) {
			$this->shipPosition = $shipPosition;
		} else {
			$this->shipPosition = new \Gameplay\Model\ShipPosition($this->userID);
		}

        $this->shipProperties->computeMaxValues();

        $oShip = new \Gameplay\Model\ShipType($this->shipProperties->ShipID);
		$this->shipSize = $oShip->Size;

		/*
		 * Dokonaj naprawy
		 */
		$this->shipProperties->autoRepair($this->userFastTimes);
	}

	public function __destruct() {
		
		$oKilledKey = new \phpCache\CacheKey('userKilled', $this->userID);
		
		$tLastKill = \phpCache\Factory::getInstance()->create()->get($oKilledKey, $this->userID);
		if (empty($tLastKill)) {
			$tLastKill = 0;
		}
		if (!is_numeric($tLastKill)) {
			$tLastKill = 0;
		}

		$tDiff = time() - $tLastKill;

		if ($tDiff < 5) {
			return;
		}

		/**
		 * Uruchom GarbageCollectora
		 */
		if ($this->shipProperties->Armor < 1) {

			global $config;
			
			\phpCache\Factory::getInstance()->create()->set($oKilledKey, time(), 10);

			$this->shipProperties->RookieTurns = $config ['combat'] ['killRookieTurns'];

			/**
			 * Utrata kaski
			 */
			$this->userStats->Cash = floor ( $this->userStats->Cash / 2 );

			/**
			 * Utrata doświadczenia
			 */
            $this->userStats->decExperience(combat::sComputeExperienceLoss($this->userStats) );

			$sectorCargo = new sectorCargo($this->shipPosition);

			/*
			 * Pętla po uzbrojeniu
			 */
			$tQuery = $this->shipWeapons->get ( 'all' );
			while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				if (additional::checkRand ( $config ['combat'] ['weaponDestroyProbability'], 100 )) {
					/*
					 * Zniszcz broń
					 */
					$this->shipWeapons->remove ( $tResult->ShipWeaponID, $this->shipProperties );
				}elseif (additional::checkRand ( $config ['combat'] ['weaponToSpaceProbability'], 100 )) {
					/*
					 * Wyrzuć w przestrzeń
					 */
					$sectorCargo->insert ( 'weapon', $tResult->WeaponID, 1 );
					$this->shipWeapons->remove ( $tResult->ShipWeaponID, $this->shipProperties );
				}
					
			}
			/*
			 * Usuń broń
			 */
			$this->shipWeapons->damageAll ();

			/*
			 * Pętla po equipie
			 */
			$tQuery = $this->shipEquipment->get ( 'all' );
			while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

				if (additional::checkRand ( $config ['combat'] ['equipmentDestroyProbability'], 100 )) {
					/*
					 * Zniszcz equipment
					 */
					$this->shipEquipment->remove ( $tResult->ShipEquipmentID, $this->shipProperties );
				}elseif (additional::checkRand ( $config ['combat'] ['equipmentToSpaceProbability'], 100 )) {
					/*
					 * Wyrzuć w przestrzeń
					 */
					$sectorCargo->insert ( 'equipment', $tResult->EquipmentID, 1);
					$this->shipEquipment->remove ( $tResult->ShipEquipmentID, $this->shipProperties );
				}
					
			}
			/*
			 * Usuń broń
			 */
			$this->shipEquipment->damageAll ();

			/**
			 * Wyrzuć itemy
			 */
			$tQuery = $this->shipCargo->getItems ();
			while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				if (additional::checkRand ( $config ['combat'] ['itemToSpaceProbability'], 100 )) {
					/*
					 * Wyrzuć w przestrzeń
					 */
					$sectorCargo->insert ( 'item', $tResult->ID, $tResult->Amount);
				}
			}

			/*
			 * Opróżnij ładownię
			 */
			$this->shipCargo->removeAll ( $this->shipProperties );

			/**
			 * Wrzuć junk
			 */
			$sectorCargo->insert ( 'product', 10, ($this->shipProperties->ArmorMax / 2));
			$sectorCargo->insert ( 'product', 11, ($this->shipProperties->ArmorMax / 3));
			$sectorCargo->insert ( 'product', 13, ($this->shipProperties->ArmorMax / 3));

            $this->shipProperties->computeMaxValues();
			$this->shipProperties->Armor = 1;
			$this->shipWeapons->computeOffensiveRating($this->shipProperties);
            $this->shipProperties->computeDefensiveRating();

			/*
			 * Dezaktywuj moje combatlocki
			 */
			combat::sDisengage ( $this->userID );
		}

		try {
			$this->userFastTimes->synchronize();
			$this->userTimes->synchronize();
			$this->shipProperties->synchronize();
			$this->userStats->synchronize();
		} catch ( Exception $e ) {
			\phpCache\Factory::getInstance()->create()->clearAll();
            echo $e->getMessage ();
            throw new \Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

	}

	public function setLastAction($action) {

		$value = array();
		$value['name'] = $action;
		$value['time'] = time();
		
		\phpCache\Factory::getInstance()->create()->set(new \phpCache\CacheKey('combatLastAction', $this->userID), $value, 60);
	}

	public function getLastAction() {

		global $config;

		$value = \phpCache\Factory::getInstance()->create()->get('combatLastAction', $this->userID);

		if ((time() - $value['time']) > $config ['combat'] ['salvoInterval']) {
			$value['name'] = '';
		}

		return $value['name'];
	}

}
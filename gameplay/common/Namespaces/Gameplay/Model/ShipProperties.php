<?php

namespace Gameplay\Model;

use Gameplay\PlayerModelProvider;

class ShipProperties extends Standard {

    protected $tableName = "userships";
    protected $tableID = "UserID";
    protected $tableUseFields = array ('Targetting', 'Scan', 'Cloak', "ArmorStrength", "ArmorPiercing", "Emp", "EmpMax", "Maneuver", "OffRating", "DefRating", "ShipName", "ShipID", "ShieldRegeneration", "PowerRegeneration", "ArmorRegeneration", "Shield", "ShieldMax", "Armor", "ArmorMax", "Power", "PowerMax", "Cargo", "CargoMax", "CurrentWeapons", "MaxWeapons", "CurrentEquipment", "MaxEquipment", "Gather", "Turns", "Speed", "RookieTurns", "SpecializationID", "CanRepairWeapons", "CanRepairEquipment", "Squadron", 'CanActiveScan', 'CanWarpJump', 'ShieldRepair', 'ArmorRepair', 'PowerRepair');
    protected $cacheExpire = 1440;

    /**
     * @var int
     */
    public $Targetting;

    /**
     * @var int
     */
    public $Scan;

    /**
     * @var int
     */
    public $Cloak;

    /**
     * @var int
     */
    public $ArmorStrength;

    /**
     * @var int
     */
    public $ArmorPiercing;

    /**
     * @var int
     */
    public $Emp;

    /**
     * @var int
     */
    public $EmpMax;

    /**
     * @var int
     */
    public $Maneuver;

    /**
     * @var int
     */
    public $OffRating;

    /**
     * @var int
     */
    public $DefRating;

    /**
     * @var string
     */
    public $ShipName;

    /**
     * @var int
     */
    public $ShipID;

    /**
     * @var int
     */
    public $ShieldRegeneration;

    /**
     * @var int
     */
    public $PowerRegeneration;

    /**
     * @var int
     */
    public $ArmorRegeneration;

    /**
     * @var int
     */
    public $Shield;

    /**
     * @var int
     */
    public $ShieldMax;

    /**
     * @var int
     */
    public $Armor;

    /**
     * @var int
     */
    public $ArmorMax;

    /**
     * @var int
     */
    public $Power;

    /**
     * @var int
     */
    public $PowerMax;

    /**
     * @var int
     */
    public $Cargo;

    /**
     * @var int
     */
    public $CargoMax;

    /**
     * @var int
     */
    public $CurrentWeapons;

    /**
     * @var int
     */
    public $MaxWeapons;

    public $CurrentEquipment;

    public $MaxEquipment;

    public $Gather;

    public $Turns;

    public $Speed;

    public $RookieTurns;

    /**
     * @var int
     */
    public $SpecializationID;

    /**
     * @var string
     */
    public $SpecializationName;

    public $CanRepairWeapons;

    public $CanRepairEquipment;

    public $Squadron;

    public $CanActiveScan;

    public $CanWarpJump;

    /**
     * @var int
     */
    public $ShieldRepair;

    /**
     * @var int
     */
    public $ArmorRepair;

    /**
     * @var int
     */
    public $PowerRepair;

    /**
     * @var string
     */
    public $ShipTypeName;

    /**
     * @param ShipProperties $shipProperties
     * @return boolean
     */
    static public function sDropRookie($shipProperties) {

        global $userID;

        $shipProperties->RookieTurns = 0;
        $shipProperties->synchronize();

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
                 $template->add ( 'ShieldRepairButton', \General\Controls::renderImgButton ( 'repair', "Playpulsar.gameplay.execute('stationRepair','Shield','{$return}',null,null);", \TranslateController::getDefault()->get ( 'RepairFor' ) . ($config ['repairCost'] ['shield'] * ($shipProperties->ShieldMax - $shipProperties->Shield)) . '$' ) );
            } else {
                $template->add ( 'ShieldRepairButton', '&nbsp;' );
            }
            if ($shipProperties->Armor < $shipProperties->ArmorMax && $userStats->Cash > ($config ['repairCost'] ['armor'] * ($shipProperties->ArmorMax < $shipProperties->Armor))) {
                $template->add ( 'ArmorRepairButton', \General\Controls::renderImgButton ( 'repair', "Playpulsar.gameplay.execute('stationRepair','Armor','{$return}',null,null);", \TranslateController::getDefault()->get ( 'RepairFor' ) . ($config ['repairCost'] ['armor'] * ($shipProperties->ArmorMax - $shipProperties->Armor)) . '$' ) );
            } else {
                $template->add ( 'ArmorRepairButton', '&nbsp;' );
            }
            if ($shipProperties->Power < $shipProperties->PowerMax && $userStats->Cash > ($config ['repairCost'] ['power'] * ($shipProperties->PowerMax < $shipProperties->Power))) {
                $template->add ( 'PowerRepairButton', \General\Controls::renderImgButton ( 'repair', "Playpulsar.gameplay.execute('stationRepair','Power','{$return}',null,null);", \TranslateController::getDefault()->get ( 'RepairFor' ) . ($config ['repairCost'] ['power'] * ($shipProperties->PowerMax - $shipProperties->Power)) . '$' ) );
            } else {
                $template->add ( 'PowerRepairButton', '&nbsp;' );
            }
            if ($shipProperties->Emp > 0 && $userStats->Cash > ($config ['repairCost'] ['emp'] * ($shipProperties->EmpMax < $shipProperties->Emp))) {
                $template->add ( 'EmpRepairButton', \General\Controls::renderImgButton ( 'repair', "Playpulsar.gameplay.execute('stationRepair','Emp','{$return}',null,null);", \TranslateController::getDefault()->get ( 'RepairFor' ) . ($config ['repairCost'] ['emp'] * $shipProperties->Emp) . '$' ) );
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
     * @throws \securityException
     */
    static public function sStationRepair() {

        global $action, $value, $config, $subaction, $userStats;

        $shipPosition   = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');
        $shipProperties = \Gameplay\PlayerModelProvider::getInstance()->get('ShipProperties');
        $portProperties = \Gameplay\PlayerModelProvider::getInstance()->get('PortEntity');

        /*
         * Warunki
         */
        if ($shipPosition->Docked != 'yes') {
            throw new \securityException();
        }

        if (empty ( $portProperties->PortID )) {
            throw new \securityException();
        }

        /*
         * Dokonaj naprawy
         */
        switch ($subaction) {

            case 'Shield' :
                $tPrice = ($shipProperties->ShieldMax - $shipProperties->Shield) * $config ['repairCost'] ['shield'];

                if ($tPrice < 0) {
                    throw new \securityException ( );
                }

                if ($userStats->Cash < $tPrice) {
                    throw new \securityException ( );
                }

                $shipProperties->Shield = $shipProperties->ShieldMax;
                $userStats->Cash -= $tPrice;
                break;

            case 'Armor' :
                $tPrice = ($shipProperties->ArmorMax - $shipProperties->Armor) * $config ['repairCost'] ['armor'];

                if ($tPrice < 0) {
                    throw new \securityException ( );
                }

                if ($userStats->Cash < $tPrice) {
                    throw new \securityException ( );
                }

                $shipProperties->Armor = $shipProperties->ArmorMax;
                $userStats->Cash -= $tPrice;
                break;

            case 'Power' :
                $tPrice = ($shipProperties->PowerMax - $shipProperties->Power) * $config ['repairCost'] ['power'];

                if ($tPrice < 0) {
                    throw new \securityException ( );
                }

                if ($userStats->Cash < $tPrice) {
                    throw new \securityException ( );
                }

                $shipProperties->Power = $shipProperties->PowerMax;
                $userStats->Cash -= $tPrice;
                break;

            case 'Emp' :
                $tPrice = $shipProperties->Emp * $config ['repairCost'] ['emp'];

                if ($tPrice < 0) {
                    throw new \securityException ( );
                }

                if ($userStats->Cash < $tPrice) {
                    throw new \securityException ( );
                }

                $shipProperties->Emp = 0;
                $userStats->Cash -= $tPrice;
                break;

            default :
                throw new \securityException ( );
                break;

        }

        if ($value == 'summary') {
            \shipEquipmentRegistry::sRender ();
        } elseif ($value == 'hangar') {
            $action = "portHangar";
        }

        return true;
    }

    /**
     * Sprawdzenie, czy statek nie jest uszkodzony przez EMP
     *
     * @return boolean
     */
    public function checkMalfunction() {
        return \additional::checkRand($this->Emp, $this->EmpMax);
    }

    /**
     * @param ShipProperties $shipProperties
     * @param UserStatistics $userStats
     * @param ShipProperties $otherShipProperties
     * @param UserStatistics $otherUserStats
     * @param SectorEntity $sectorProperties
     * @return boolean
     */
    static public function sGetVisibility(ShipProperties $shipProperties, UserStatistics $userStats, ShipProperties $otherShipProperties, UserStatistics $otherUserStats, SectorEntity $sectorProperties) {

        $percentage = $sectorProperties->Visibility + $userStats->Level - $otherUserStats->Level + $shipProperties->Scan - $otherShipProperties->Cloak;

        if ($percentage < 1) {
            $percentage = 1;
        }

        if ($percentage > 99) {
            $percentage = 99;
        }

        return \additional::checkRand ( $percentage, 100 );
    }

    public function computeDefensiveRating() {
        $this->DefRating = floor ( ($this->Shield + $this->Armor) / 100 );
    }

    /**
     * Przeliczenie wykorzystanej przestrzeni ładowni
     *
     * @param ShipProperties $shipProperties
     */
    static public function updateUsedCargo(ShipProperties $shipProperties) {
        $item = new \shipCargo ( $shipProperties->getEntryId() );
        $shipProperties->Cargo = $item->getUsage();
    }

    public function setFromFull() {
        $this->Shield = $this->ShieldMax;
        $this->Armor = $this->ArmorMax;
        $this->Power = $this->PowerMax;
        $this->Emp = 0;
    }

    /**
     * Statyczne przeliczenie wszystkich max values
     * @param int $userID
     */
    static public function sQuickRecompute($userID) {

        $shipProperties = new ShipProperties($userID);

        $shipProperties->computeMaxValues();
        $shipProperties->updateWeaponsCount();
        $shipProperties->updateEquipmentsCount();
        $shipProperties->synchronize();
        self::sFlushCache($userID);
    }

    /**
     * @param ShipProperties $shipProperties
     */
    static public function sRecomputeValues(ShipProperties $shipProperties) {
        $shipProperties->computeMaxValues();
        $shipProperties->updateWeaponsCount();
        $shipProperties->updateEquipmentsCount();
    }

    public function updateWeaponsCount() {
        $tQuery = "SELECT COUNT(*) AS ile FROM shipweapons WHERE UserID='{$this->entryId}'";
        $tQuery = $this->db->execute ( $tQuery );
        while ( $tResult = $this->db->fetch ( $tQuery ) ) {
            $this->CurrentWeapons = $tResult->ile;
        }
    }

    public function updateEquipmentsCount() {
        $tQuery = "SELECT COUNT(*) AS ile FROM shipequipment WHERE UserID='{$this->entryId}'";
        $tQuery = $this->db->execute ( $tQuery );
        while ( $tResult = $this->db->fetch ( $tQuery ) ) {
            $this->CurrentEquipment = $tResult->ile;
        }
    }


    public function computeMaxValues() {

        $tShip = new ShipType($this->ShipID);

        $this->ShieldMax = $tShip->Shield;
        $this->ArmorMax = $tShip->Armor;
        $this->PowerMax = $tShip->Power;
        $this->Speed = $tShip->Speed;
        $this->Maneuver = $tShip->Maneuver;
        $this->CargoMax = $tShip->Cargo;
        $this->MaxEquipment = $tShip->Space;
        $this->MaxWeapons = $tShip->Weapons;
        $this->Scan = $tShip->Scan;
        $this->Cloak = $tShip->Cloak;
        $this->Gather = $tShip->Gather;
        $this->ArmorStrength = $tShip->ArmorStrength;
        $this->ArmorPiercing = $tShip->ArmorPiercing;
        $this->ShieldRegeneration = $tShip->ShieldRegeneration;
        $this->ArmorRegeneration = $tShip->ArmorRegeneration;
        $this->PowerRegeneration = $tShip->PowerRegeneration;
        $this->ShieldRepair = $tShip->ShieldRepair;
        $this->ArmorRepair = $tShip->ArmorRepair;
        $this->PowerRepair = $tShip->PowerRepair;
        $this->EmpMax = $tShip->Emp;
        $this->Targetting = $tShip->Targetting;
        $this->CanWarpJump = $tShip->CanWarpJump;
        $this->CanActiveScan = $tShip->CanActiveScan;

        $equipmentList = new ShipEquipments($this->getEntryId());

        $tResult = $equipmentList->get ( "working" );
        while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tResult ) ) {
            $this->ShieldMax += $resultRow->Shield;
            $this->ArmorMax += $resultRow->Armor;
            $this->PowerMax += $resultRow->Power;
            $this->EmpMax += $resultRow->Emp;
            $this->Speed += $resultRow->Speed;
            $this->Maneuver += $resultRow->Maneuver;
            $this->CargoMax += $resultRow->Cargo;
            $this->MaxEquipment += $resultRow->Space;
            $this->MaxWeapons += $resultRow->Weapons;
            $this->Scan += $resultRow->Scan;
            $this->Cloak += $resultRow->Cloak;
            $this->Gather += $resultRow->Gather;
            $this->ArmorStrength += $resultRow->ArmorStrength;
            $this->ArmorPiercing += $resultRow->ArmorPiercing;
            $this->ShieldRegeneration += $resultRow->ShieldRegeneration;
            $this->ArmorRegeneration += $resultRow->ArmorRegeneration;
            $this->PowerRegeneration += $resultRow->PowerRegeneration;
            $this->ShieldRepair += $resultRow->ShieldRepair;
            $this->ArmorRepair += $resultRow->ArmorRepair;
            $this->PowerRepair += $resultRow->PowerRepair;
            $this->Targetting += $resultRow->Targetting;
            $this->CanWarpJump += $resultRow->CanWarpJump;
            $this->CanActiveScan += $resultRow->CanActiveScan;
        }

        $this->cutProperties();
    }

    public function cutProperties() {
        if ($this->Shield > $this->ShieldMax) {
            $this->Shield = $this->ShieldMax;
        }
        if ($this->Armor > $this->ArmorMax) {
            $this->Armor = $this->ArmorMax;
        }
        if ($this->Power > $this->PowerMax) {
            $this->Power = $this->PowerMax;
        }
    }

    /**
     * @param ShipProperties $shipProperties
     * @param \Gameplay\Model\UserTimes $userTimes
     * @return boolean
     */
    public function generateTurns(ShipProperties $shipProperties, \Gameplay\Model\UserTimes $userTimes) {

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
     * @param \Gameplay\Model\UserFastTimes $userFastTimes
     * @return bool
     */
    public function autoRepair(\Gameplay\Model\UserFastTimes $userFastTimes) {

        global $config;

        $actualTime = time();

        $repaired = false;

        /*
         * Czy dokonać naprawy
         */
        if ($actualTime - $userFastTimes->LastRepair >= $config ['timeThresholds'] ['shipRepair']) {

            /*
             * Czy naprawiać Shield
             */
            if ($this->Shield < $this->ShieldMax && $this->ShieldRegeneration > 0) {

                $toRepair = ($actualTime - $userFastTimes->LastRepair) * $this->ShieldRegeneration;

                $this->Shield += $toRepair;
                if ($this->Shield > $this->ShieldMax)
                    $this->Shield = $this->ShieldMax;

                $repaired = true;
            }

            /*
             * Czy naprawiać Armor
             */
            if ($this->Armor < $this->ArmorMax && $this->ArmorRegeneration > 0) {

                $toRepair = ($actualTime - $userFastTimes->LastRepair) * $this->ArmorRegeneration;

                $this->Armor += $toRepair;
                if ($this->Armor > $this->ArmorMax)
                    $this->Armor = $this->ArmorMax;

                $repaired = true;
            }

            /*
             * Czy generować Power
             */
            if ($this->Power < $this->PowerMax && $this->PowerRegeneration > 0) {

                $toRepair = ($actualTime - $userFastTimes->LastRepair) * $this->PowerRegeneration;

                $this->Power += $toRepair;
                if ($this->Power > $this->PowerMax)
                    $this->Power = $this->PowerMax;

                $repaired = true;
            }

            /*
             * Czy naprawiać EMP
             */
            if ($this->Emp > 0) {
                $toRepair = ($actualTime - $userFastTimes->LastRepair) * $config ['emp'] ['repairRatio'];
                $this->Emp -= $toRepair;
                if ($this->Emp < 0) {
                    $this->Emp = 0;
                }
                $repaired = true;
            }

            if ($repaired) {
                $this->computeDefensiveRating();
            }
            $userFastTimes->LastRepair = time();

        }

        return true;
    }

    function get() {
        $userProperties = \Gameplay\PlayerModelProvider::getInstance()->get('UserEntity');

        if (empty($userProperties)) {
            $userProperties = new UserEntity();
        }

        if (empty($userProperties->Language)) {
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
                userships.ShieldRepair AS ShieldRepair,
                userships.PowerRepair AS PowerRepair,
                userships.ArmorRepair AS ArmorRepair,
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
            WHERE
                userships.UserID='{$this->dbID}'";

        $tResult = $this->db->execute($tQuery);
        while($resultRow = $this->db->fetch($tResult)) {
            $this->loadData($resultRow, false);
        }
        return true;
    }

    /**
     * Update off i deff rating okrętu
     *
     * @param int $userID
     */
    static public function sUpdateRating(/** @noinspection PhpUnusedParameterInspection */
        $userID) {

        $shipWeapons = PlayerModelProvider::getInstance()->get('ShipWeapons');
        $shipProperties = PlayerModelProvider::getInstance()->get('ShipProperties');

        $shipWeapons->computeOffensiveRating($shipProperties);
        $shipProperties->computeDefensiveRating();
    }

    /**
     * Zwraca wartość statku gracza
     *
     * @param int $userID
     * @return int
     */
    static public function sGetValue($userID) {

        $oDb            = \Database\Controller::getInstance();
        $shipProperties = new ShipProperties($userID);
        $oShip          = new ShipType($shipProperties->ShipID);

        $retVal = $oShip->Price;

        $tQuery = "SELECT
                SUM(weapontypes.Price) AS Value
            FROM
                shipweapons JOIN weapontypes USING(WeaponID)
            WHERE
                shipweapons.UserID = '{$userID}'";
        $tQuery = $oDb->execute ( $tQuery );
        while ( $tResult = $oDb->fetch ( $tQuery ) ) {
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
        $tQuery = $oDb->execute ( $tQuery );
        while ( $tResult = $oDb->fetch ( $tQuery ) ) {
            $retVal += $tResult->Value;
        }

        return $retVal;
    }

    /**
     * @param int $shipID
     * @throws \securityException
     */
    static public function sBuy($shipID) {

        global $shipCargo, $userProperties, $action, $userStats;

        $shipPosition   = PlayerModelProvider::getInstance()->get('ShipPosition');
        $shipProperties = PlayerModelProvider::getInstance()->get('ShipProperties');
        $portProperties = PlayerModelProvider::getInstance()->get('PortEntity');
        $userProperties = PlayerModelProvider::getInstance()->get('UserEntity');
        $shipWeapons    = PlayerModelProvider::getInstance()->get('ShipWeapons');
        $shipEquipment  = PlayerModelProvider::getInstance()->get('ShipEquipments');

        if ($shipPosition->Docked == 'no') {
            throw new \securityException ( );
        }

        if ($portProperties->Type != 'station') {
            throw new \securityException ( );
        }

        $tShip = new ShipType($shipID);

        $currentShipValue = floor( self::sGetValue( $userProperties->UserID ) / 2 );

        if ($userStats->Cash + $currentShipValue < $tShip->Price) {
            throw new \securityException ( );
        }

        if ($userStats->Fame < $tShip->Fame) {
            throw new \securityException ( );
        }

        /**
         * czy port sprzedaje
         */
        $tString = ',' . $portProperties->Ships . ',';
        if (mb_strpos ( $tString, ',' . $shipID . ',' ) === false) {
            throw new \securityException ( );
        }

        $shipProperties->ShipID = $shipID;
        $shipProperties->synchronize();

        $userStats->decCash($tShip->Price - $currentShipValue);
        $userStats->decFame($tShip->Fame);
        $portProperties->Cash += $tShip->Price;
        $shipEquipment->removeAll($shipProperties);
        $shipWeapons->removeAll($shipProperties);
        $shipCargo->removeAll($shipProperties);

        $shipProperties->computeMaxValues();
        $shipProperties->setFromFull();
        $shipWeapons->computeOffensiveRating ( $shipProperties );
        $shipProperties->computeDefensiveRating();

        $action = "portHangar";

        \Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:shipBought}' . $tShip->Price . '$' );
    }
}
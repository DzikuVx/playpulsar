<?php

namespace Gameplay\Model;

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

    public $SpecializationID;

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

        global $action, $value, $config, $portProperties, $subaction, $userStats;

        $shipPosition   = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');
        $shipProperties = \Gameplay\PlayerModelProvider::getInstance()->get('ShipProperties');

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
     * @param ShipProperties $shipProperties
     * @return boolean
     */
    static public function sCheckMalfunction(ShipProperties $shipProperties) {
        return \additional::checkRand ( $shipProperties->Emp, $shipProperties->EmpMax );
    }

    /**
     * @param ShipProperties $shipProperties
     * @param \stdClass $userStats
     * @param ShipProperties $otherShipProperties
     * @param \stdClass $otherUserStats
     * @param \stdClass $sectorProperties
     * @return boolean
     */
    static public function sGetVisibility(ShipProperties $shipProperties, $userStats, ShipProperties $otherShipProperties, $otherUserStats, $sectorProperties) {

        $percentage = $sectorProperties->Visibility + $userStats->Level - $otherUserStats->Level + $shipProperties->Scan - $otherShipProperties->Cloak;

        if ($percentage < 1) {
            $percentage = 1;
        }

        if ($percentage > 99) {
            $percentage = 99;
        }

        return \additional::checkRand ( $percentage, 100 );
    }

    /**
     * @param ShipProperties $shipProperties
     */
    static function computeDefensiveRating(ShipProperties $shipProperties) {
        if (!empty($shipProperties)) {
            $shipProperties->DefRating = floor ( ($shipProperties->Shield + $shipProperties->Armor) / 100 );
        }
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

    /**
     * Ustawienie wartości maksymalnych jako aktualne
     * @param ShipProperties $shipProperties
     */
    static function setFromFull(ShipProperties $shipProperties) {
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

        $shipProperties = new ShipProperties($userID);

        self::computeMaxValues($shipProperties);
        \shipWeapons::sUpdateCount($shipProperties, $userID);
        \shipEquipment::sUpdateCount($shipProperties, $userID);
        $shipProperties->synchronize();
        self::sFlushCache($userID);
    }

    /**
     * @param ShipProperties $shipProperties
     * @param int $userID
     */
    static public function sRecomputeValues(ShipProperties $shipProperties, $userID) {
        self::computeMaxValues($shipProperties);
        \shipWeapons::sUpdateCount($shipProperties, $userID);
        \shipEquipment::sUpdateCount($shipProperties, $userID);
    }

    /**
     * @param ShipProperties $shipProperties
     */
    static function computeMaxValues(ShipProperties $shipProperties) {

        $tShip = \ship::quickLoad ( $shipProperties->ShipID );

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

        $equipmentList = new \shipEquipment($shipProperties->getEntryId());

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
     * @param ShipProperties $shipProperties
     */
    static public function sCutProperties(ShipProperties $shipProperties) {

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
                self::computeDefensiveRating ( $this );
            }
            $userFastTimes->LastRepair = time();

        }

        return true;
    }

    function get() {

        global $userProperties;

        if (empty($userProperties)) {
            $userProperties = new \stdClass();
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

        $oDb            = \Database\Controller::getInstance();
        $shipProperties = new ShipProperties($userID);
        $oShip          = \ship::quickLoad($shipProperties->ShipID);

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

        global $shipCargo, $shipWeapons, $userProperties, $action, $userStats, $portProperties, $shipEquipment;

        $shipPosition   = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');
        $shipProperties = \Gameplay\PlayerModelProvider::getInstance()->get('ShipProperties');

        if ($shipPosition->Docked == 'no') {
            throw new \securityException ( );
        }

        if ($portProperties->Type != 'station') {
            throw new \securityException ( );
        }

        $tShip = \ship::quickLoad ( $shipID );

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

        \userStats::decCash ( $userStats, $tShip->Price - $currentShipValue );
        \userStats::decFame ( $userStats, $tShip->Fame );
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
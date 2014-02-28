<?php

namespace Gameplay\Model;

use Gameplay\Exception\Model;
use Gameplay\PlayerModelProvider;

class PortEntity extends CustomGet {
    protected $tableName = "ports";
    protected $tableID = "PortID";
    protected $tableUseFields = array ('ResetTime', 'Shield', 'Armor', 'DefRating', 'OffRating', 'Cash', 'Experience', 'Level', 'Raided', 'State', 'System', 'X', 'Y', 'PortTypeID');

    /**
     * @var int
     */
    public $ResetTime;

    /**
     * @var int
     */
    public $Shield;

    /**
     * @var int
     */
    public $Armor;

    /**
     * @var int
     */
    public $DefRating;

    /**
     * @var int
     */
    public $OffRating;

    /**
     * @var int
     */
    public $Cash;

    /**
     * @var int
     */
    public $Experience;

    /**
     * @var int
     */
    public $Level;

    /**
     * @var int
     */
    public $Raided;

    /**
     * @var string
     */
    public $State;

    /**
     * @var string
     */
    public $System;

    /**
     * @var int
     */
    public $X;

    /**
     * @var int
     */
    public $Y;

    /**
     * @var int
     */
    public $PortTypeID;

    /**
     * @var int
     */
    public $PortID;

    /**
     * @var string
     */
    public $Weapons;

    /**
     * @var string
     */
    public $Equipment;

    public $Items;

    public $Ships;

    /**
     * @var string
     */
    public $SpecialBuy;

    /**
     * @var string
     */
    public $SpecialSell;

    /**
     * @var string
     */
    public $Type;

    /**
     * @var string
     */
    public $NoBuy;

    /**
     * @var string
     */
    public $NoSell;

    /**
     * @var string
     */
    public $PortTypeName;

    /**
     * @var string
     */
    public $Image;

    /**
     * @var string
     */
    public $Name;

    public $WeaponSize;

    /**
     * Panel portu
     * @param int $userID
     * @param \Gameplay\Model\ShipPosition $shipPosition
     * @param PortEntity $portProperties
     * @param string $action
     * @param string $subaction
     * @param string $value
     * @param string $id
     * @return string
     */
    static public function sPopulatePanel(/** @noinspection PhpUnusedParameterInspection */
        $userID, \Gameplay\Model\ShipPosition $shipPosition, PortEntity $portProperties, $action, $subaction, $value, $id) {

        if ($shipPosition->Docked == 'no') {
            return false;
        }

        $sRetVal = '<div style="margin: 0 auto;">';

        switch ($portProperties->Type) {

            case "station" :
                if ($action == "portHangar") {
                    $fix = "btn-inverse";
                } else {
                    $fix = "";
                }
                $sRetVal .= \General\Controls::bootstrapButton('{T:hangar}',"Playpulsar.gameplay.execute('portHangar',null,null,null,null);", $fix);

                if ($action == "portMarketplace") {
                    $fix = "btn-inverse";
                } else {
                    $fix = "";
                }
                $sRetVal .= \General\Controls::bootstrapButton('{T:marketplace}',"Playpulsar.gameplay.execute('portMarketplace',null,null,null,null);", $fix);

                if ($action == "portShipyard") {
                    $fix = "btn-inverse";
                } else {
                    $fix = "";
                }
                $sRetVal .= \General\Controls::bootstrapButton('{T:shipyard}' ,"Playpulsar.gameplay.execute('portShipyard',null,null,null,null);", $fix);

                if ($action == "portBank") {
                    $fix = "btn-inverse";
                } else {
                    $fix = "";
                }
                $sRetVal .= \General\Controls::bootstrapButton('{T:Bank}',"Playpulsar.gameplay.execute('portBank',null,null,null,null);", $fix);

                if ($action == "portStorehouse") {
                    $fix = "btn-inverse";
                } else {
                    $fix = "";
                }
                $sRetVal .= \General\Controls::bootstrapButton('{T:storehouse}',"Playpulsar.gameplay.execute('portStorehouse',null,null,null,null);", $fix);
                break;

            case "port" :
                if ($action == "portHangar") {
                    $fix = "btn-inverse";
                } else {
                    $fix = "";
                }
                $sRetVal .= \General\Controls::bootstrapButton('{T:hangar}',"Playpulsar.gameplay.execute('portHangar',null,null,null,null);", $fix);

                if ($action == "portMarketplace") {
                    $fix = "btn-inverse";
                } else {
                    $fix = "";
                }
                $sRetVal .= \General\Controls::bootstrapButton('{T:marketplace}',"Playpulsar.gameplay.execute('portMarketplace',null,null,null,null);", $fix);

                if ($action == "portStorehouse") {
                    $fix = "btn-inverse";
                } else {
                    $fix = "";
                }

                $sRetVal .= \General\Controls::bootstrapButton('{T:storehouse}',"Playpulsar.gameplay.execute('portStorehouse',null,null,null,null);", $fix);
                break;
        }


        $sRetVal .= '</div>';

        $sRetVal .= "<div id=\"portContent\">";
        \Gameplay\Panel\PortAction::getInstance()->add($sRetVal);

        if (file_exists("../engine/inc/" . $action . ".php" )) {
            /** @noinspection PhpIncludeInspection */
            include "../engine/inc/" . $action . ".php";
        }

        $sRetVal = "</div>";
        \Gameplay\Panel\PortAction::getInstance()->add($sRetVal);

        return true;
    }

    //FIXME replace with dynamic method
    /**
     * @param PortEntity $portProperties
     * @param int $weaponID
     * @return boolean
     */
    static public function sCheckWeapon(PortEntity $portProperties, $weaponID) {

        $tArray = explode(',', $portProperties->Weapons);

        if (array_search($weaponID, $tArray) === false) {
            $retVal = false;
        } else {
            $retVal = true;
        }

        return $retVal;
    }

    /**
     * @throws Model
     * @return boolean
     */
    function get() {
        $userProperties = PlayerModelProvider::getInstance()->get('UserEntity');

        $nameField = "Name" . strtoupper ( $userProperties->Language );

        if (!is_numeric($this->entryId)) {
            $whereCondition = "
    	        ports.System = '{$this->entryId->System}' AND
                ports.X = '{$this->entryId->X}' AND
                ports.Y = '{$this->entryId->Y}' ";
        } else {
            $whereCondition = " ports.PortID = '{$this->dbID}' ";
        }

        $tResult = $this->db->execute("
          SELECT
            ports.PortID AS PortID,
            ports.PortTypeID AS PortTypeID,
            porttypes.$nameField AS PortTypeName,
            ports.$nameField AS Name,
            ports.ResetTime AS ResetTime,
            porttypes.Type AS Type,
            porttypes.Items AS Items,
            porttypes.Weapons AS Weapons,
            porttypes.Equipment AS Equipment,
            porttypes.Ships AS Ships,
            porttypes.Image AS Image,
            ports.Shield AS Shield,
            ports.Armor AS Armor,
            ports.OffRating AS OffRating,
            ports.DefRating AS DefRating,
            ports.Cash AS Cash,
            ports.Raided AS Raided,
            porttypes.SpecialBuy AS SpecialBuy,
            porttypes.SpecialSell AS SpecialSell,
            porttypes.NoBuy AS NoBuy,
            porttypes.NoSell AS NoSell,
            ports.Experience AS Experience,
            ports.Level AS Level,
            ports.State AS State,
            ports.System,
            ports.X,
            ports.Y
          FROM
            ports JOIN porttypes ON porttypes.PortTypeID = ports.PortTypeID
          WHERE
            " . $whereCondition . "
          LIMIT 1");

        while($resultRow = $this->db->fetch($tResult)) {
            $this->loadData($resultRow, false);
        }

        return true;
    }

    /**
     * @param int|ShipPosition $ID
     * @return string
     */
    protected function parseCacheID($ID) {

        if (!is_numeric($ID)) {
            return md5($ID->System . "/" . $ID->X . "/" . $ID->Y );
        } else {
            return "ID:" . $ID;
        }
    }

    /**
     * @param int|\stdClass $ID
     * @return int|string
     */
    protected function parseDbID($ID) {
        return $this->PortID;
    }

    /**
     * Obliczenie level portu
     *
     * @param int $exp
     * @return int
     */
    static function computeLevel($exp) {

        global $config;

        $f_out = floor ( pow ( ($exp / 10000), (1 / 3) ) ) + $config ['port'] ['levelMin'];
        if ($f_out > $config ['port'] ['levelMax'])
            $f_out = $config ['port'] ['levelMax'];
        return $f_out;
    }

    //TODO replace with dynamic method
    /**
     * @param PortEntity $portProperties
     */
    static public function sCheckNewLevel(PortEntity $portProperties) {
        if ($portProperties->Cash < 0) {
            $portProperties->Cash = 0;
        }

        //Oblicz nowy level portu
        if ($portProperties->Level != self::computeLevel($portProperties->Experience)) {
            $portProperties->Level = self::computeLevel($portProperties->Experience);
            self::sResetWeapons($portProperties);
            self::sUpdateRating($portProperties);
        }
    }

    /**
     * @param PortEntity $portProperties
     * @throws \Database\Exception
     */
    static private function sResetWeapons(PortEntity $portProperties) {

        //Oblicz liczbę broni każdego typu
        $weaponNumber = floor ( $portProperties->Level * 1.3 ) + 2;

        \Database\Controller::getInstance()->disableAutocommit();

        //Zrzuć uzbrojenie tego portu
        $tQuery = "DELETE FROM portweapons WHERE PortID='{$portProperties->PortID}'";
        \Database\Controller::getInstance()->execute ( $tQuery );

        $sPreparedInsert = mysqli_prepare(\Database\Controller::getInstance()->getHandle(), "INSERT INTO portweapons(PortID, WeaponID) Values(?,?)");

        //Znajdz wszystkie typy broni jakie umieścić w porcie
        $tQuery = "SELECT WeaponID FROM weapontypes WHERE PortWeapon='yes' AND Active='yes' ORDER BY PortPriority ASC";
        $tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
        while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
            //Wstaw adekwatną liczbę broni do portu
            for($tIndex = 0; $tIndex < $weaponNumber; $tIndex ++) {

                mysqli_stmt_bind_param($sPreparedInsert, 'ii', $portProperties->PortID, $tR1->WeaponID );
                $tResult = mysqli_stmt_execute($sPreparedInsert);

                if (empty($tResult)) {
                    throw new \Database\Exception ( mysqli_error (\Database\Controller::getInstance()->getHandle()), mysqli_errno (\Database\Controller::getInstance()->getHandle()) );
                }

            }
        }

        \Database\Controller::getInstance()->commit();
        \Database\Controller::getInstance()->enableAutocommit();

    }

    /**
     * Reset portu
     *
     * @param bool $force - force reset, even when time has not passed
     * @return bool
     */
    public function reset($force = false) {

        global $actualTime, $config;

        $oDb = \Database\Controller::getInstance();

        if ($this->PortID != null && ($force || $actualTime - $this->ResetTime > $config ['timeThresholds'] ['portReset'])) {

            try{

                $portCargoHalfAmount = floor ( $config ['port'] ['maxCargoAmount'] / 2 );

                $oDb->disableAutocommit();

                $sPreparedInsert = mysqli_prepare($oDb->getHandle(), "INSERT INTO portcargo(PortID, CargoID, Amount, Type, Mode, UserID)VALUES (?,?,?,'product',?,null)");
                $sPreparedUpdate = mysqli_prepare($oDb->getHandle(), "UPDATE portcargo SET Amount = ?, Mode = ? WHERE PortID = ? AND CargoID = ? AND Type='product' AND portcargo.UserID IS NULL");

                //Pętla po wszystkich towarach
                $tQuery = "SELECT
                    products.ProductID AS ProductID,
                    products.RegularSell AS RegularSell,
                    products.RegularBuy AS RegularBuy,
                    portcargo.Amount AS Amount,
                    portcargo.Mode AS Mode
                  FROM
                    products LEFT JOIN portcargo ON portcargo.CargoID=products.ProductID AND portcargo.Type='product' AND portcargo.PortID='{$this->PortID}' AND portcargo.UserID IS NULL
                  WHERE
                    products.Active = 'yes'
                  ";
                $tQuery = $oDb->execute($tQuery);
                while($tR1 = $oDb->fetch ( $tQuery ) ) {
                    $entryExists = true;
                    if ($tR1->Amount == null) {
                        $tR1->Amount = 0;
                        $tR1->Mode = 'buy';
                        $entryExists = false;
                    }
                    $switchDirection = false;
                    $setToMax = false;
                    $setToMin = false;
                    $increase = false;
                    $decrease = false;

                    //Tutaj logika ustalania wartości

                    //Odwracajnie kolejności
                    if ($tR1->RegularSell == 'yes' && $tR1->RegularBuy == 'yes')
                        switch ($tR1->Mode) {
                            case 'buy' :
                                if ($tR1->Amount > $portCargoHalfAmount) {
                                    //Jesli port jest zapełniony tym towarem, odwróć kolejność w 50%
                                    if (\additional::checkRand ( 5, 10 )) {
                                        $switchDirection = true;
                                    }
                                } else {
                                    //Jeśli nie jest zapełniony, odwróć w 5%
                                    if (\additional::checkRand ( 1, 20 )) {
                                        $switchDirection = true;
                                    }
                                }
                                break;
                            case 'sell' :
                                if ($tR1->Amount < $portCargoHalfAmount) {
                                    //Jesli port jest zapełniony tym towarem, odwróć kolejność w 50%
                                    if (\additional::checkRand ( 5, 10 )) {
                                        $switchDirection = true;
                                    }
                                } else {
                                    //Jeśli nie jest zapełniony, odwróć w 5%
                                    if (\additional::checkRand ( 1, 20 )) {
                                        $switchDirection = true;
                                    }
                                }
                                break;
                        }

                    //Wykonaj odwrócenia kierunku
                    if ($switchDirection) {
                        switch ($tR1->Mode) {
                            case 'buy' :
                                $tR1->Mode = 'sell';
                                break;
                            case 'sell' :
                                $tR1->Mode = 'buy';
                                break;
                        }
                    }

                    //Dodatkowy warunek właściwego kierunku
                    if ($tR1->RegularSell == 'no')
                        $tR1->Mode = 'buy';
                    if ($tR1->RegularBuy == 'no')
                        $tR1->Mode = 'sell';

                    //Dokonaj zmiany wartości towaru w porcie zgodnie z jego kierunkiem
                    $tRand = rand ( 1, 5 );
                    switch ($tR1->Mode) {
                        case 'buy' :
                            //Czy zmniejszyć do 0, czy zwiększyć, czy zmniejszyć normalnie
                            if ($tRand == 1) {
                                $setToMin = true;
                            }
                            if ($tRand == 2) {
                                $increase = true;
                            }
                            if ($tRand > 2) {
                                $decrease = true;
                            }
                            break;
                        case 'sell' :
                            //Czy większyć do max, czy zmniejszyć, czy zwiększyć normalnie
                            if ($tRand == 1) {
                                $setToMax = true;
                            }
                            if ($tRand == 2) {
                                $decrease = true;
                            }
                            if ($tRand > 2) {
                                $increase = true;
                            }
                            break;
                    }

                    if ($setToMax) {
                        $tR1->Amount = $config ['port'] ['maxCargoAmount'];
                    }
                    if ($setToMin) {
                        $tR1->Amount = 0;
                    }

                    if ($increase) {
                        $diff = floor ( ($config ['port'] ['maxCargoAmount'] - $tR1->Amount) / $config ['port'] ['cargoChangeRatio'] ) + rand ( 0, 20 ) - 10;
                        $tR1->Amount += $diff;
                    }
                    if ($decrease) {
                        $diff = floor ( $tR1->Amount / $config ['port'] ['cargoChangeRatio'] ) + rand ( 0, 20 ) - 10;
                        $tR1->Amount -= $diff;
                    }

                    if ($tR1->Amount < 0)
                        $tR1->Amount = 0;
                    if ($tR1->Amount > $config ['port'] ['maxCargoAmount'])
                        $tR1->Amount = $config ['port'] ['maxCargoAmount'];

                    if ($entryExists) {

                        /*
                         * Updatey
                        */
                        mysqli_stmt_bind_param($sPreparedUpdate, 'isii', $tR1->Amount, $tR1->Mode, $this->PortID, $tR1->ProductID);
                        mysqli_stmt_execute($sPreparedUpdate);

                    } else {

                        /*
                         * Insert
                        */
                        mysqli_stmt_bind_param($sPreparedInsert, 'iiis', $this->PortID, $tR1->ProductID, $tR1->Amount, $tR1->Mode);
                        mysqli_stmt_execute($sPreparedInsert);

                    }
                }

                //Wpisz czas resetu portu
                $this->ResetTime = $actualTime;

                $oDb->commit();
                $oDb->enableAutocommit();

                /*
                 * Generowanie map
                */

                /*
                 * usuń wszystkie mapy z tego portu
                */
                $oDb->execute("DELETE FROM portcargo WHERE PortID='{$this->PortID}' AND UserID IS NULL AND Type='map'");

                /*
                 * Wygeneruj nowe
                */
                if (!empty($config ['port'] ['mapCreateCount'])) {
                    $tMapCount = rand(1, $config ['port'] ['mapCreateCount']);
                    for ($tIndex = 0; $tIndex < $tMapCount; $tIndex++) {
                        $oDb->execute("INSERT INTO portcargo(PortID, CargoID, Amount, Type, Mode, UserID)VALUES ('{$this->PortID}','" . \galaxy::sGetRandomWithoutMap(SystemProperties::getGalaxy($this->System))."','1','map','buy',null)");
                    }

                }

                $this->clearCache();
            } catch (Exception $e) {
                $oDb->rollback();
                $oDb->enableAutocommit();
                \psDebug::cThrow(null, $e, array('display'=>false));
            }
        }
        return true;
    }

    //FIXME replace with dynamic method
    /**
     * @param PortEntity $portProperties
     */
    static private function sUpdateRating(PortEntity $portProperties) {
        //todo: obliczanie ratingu portu
    }

    protected function set() {

        if (empty($this->PortID)) {
            return;
        }

        $this->dbID = $this->PortID;

        if (empty($this->dbID)) {
            throw new Model('Object not initialized properly');
        }

        $this->db->execute($this->formatUpdateQuery());
    }

}
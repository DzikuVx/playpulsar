<?php

namespace Gameplay\Model;

class ShipPosition extends Standard {

    protected $tableName = "shippositions";
    protected $tableID = "UserID";
    protected $tableUseFields = array ("UserID", "System", "X", "Y", "Docked" );
    protected $cacheExpire = 3600;

    /**
     * @var int
     */
    public $UserID;

    /**
     * @var int
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
     * @var string
     */
    public $Docked;

    /**
     * Manually set coordinates
     * @param int $System
     * @param int $X
     * @param int $Y
     * @param string $Docked
     */
    public function setCoordinates($System, $X, $Y, $Docked = 'no') {
        $this->System = $System;
        $this->X      = $X;
        $this->Y      = $Y;
        $this->Docked = $Docked;
    }

    /**
     * @return Coordinates
     */
    public function getCoordinates() {
        return new Coordinates($this->System, $this->X, $this->Y, $this->Docked);
    }

}

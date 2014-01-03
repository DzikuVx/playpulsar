<?php

namespace Gameplay\Model;

class ShipPosition extends Standard {

    protected $tableName = "shippositions";
    protected $tableID = "UserID";
    protected $tableUseFields = array ("System", "X", "Y", "Docked" );
    protected $cacheExpire = 3600;

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

}

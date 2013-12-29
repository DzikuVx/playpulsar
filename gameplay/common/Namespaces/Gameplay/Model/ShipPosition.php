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

}
<?php
namespace Gameplay\Model;

class Coordinates {

    public $System;
    public $X;
    public $Y;
    public $Docked;

    /**
     * @param int $System
     * @param int $X
     * @param int $Y
     * @param string $Docked
     */
    public function __construct($System = null, $X = null, $Y = null, $Docked = null) {
        $this->System = $System;
        $this->X = $X;
        $this->Y = $Y;
        $this->Docked = $Docked;
    }
}
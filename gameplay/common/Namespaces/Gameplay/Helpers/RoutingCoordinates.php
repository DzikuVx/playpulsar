<?php

namespace Gameplay\Helpers;

class RoutingCoordinates {

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
    public $value = 0;

    /**
     * @var string
     */
    public $direction;
	
	/**
	 * @param int $X
	 * @param int $Y
	 * @param string $value
	 * @param string $direction
	 */
	public function __construct($X, $Y, $value, $direction) {

        $this->X = $X;
        $this->Y = $Y;
		$this->value = $value;
		$this->direction = $direction;
	}
}
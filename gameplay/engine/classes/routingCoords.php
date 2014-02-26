<?php

/**
 * Współrzędne routingu
 *
 * @version $Rev: 156 $
 * @package Engine
 */
class routingCoords extends simpleCoords {
	public $value = 0;
	public $direction;
	
	/**
	 * Konstruktor publiczny
	 *
	 * @param int $X
	 * @param int $Y
	 * @param string $value
	 * @param string $direction
	 */
	function __construct($X, $Y, $value, $direction) {
		
		parent::__construct ( $X, $Y );
		$this->value = $value;
		$this->direction = $direction;
	}
}
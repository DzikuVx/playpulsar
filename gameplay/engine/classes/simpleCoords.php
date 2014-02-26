<?php

/**
 * Klasa prostych koordynat 2D
 *
 * @version $Rev: 181 $
 * @package Engine
 */
class simpleCoords {
	/**
	 * Koordynata X
	 * @var int
	 */
	public $X;

	/**
	 *
	 * Koordynata Y
	 * @var int
	 */
	public $Y;

	/**
	 * @param int $X
	 * @param int $Y
	 */
	function __construct($X, $Y) {

		$this->X = $X;
		$this->Y = $Y;
	}

}
<?php

class simpleCoords {
	/**
	 * @var int
	 */
	public $X;

	/**
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
<?php
/**
 * Klasa wiadomości newsagency
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class newsAgencyMessage {

	/**
	 * System zdarzenie
	 *
	 * @var int
	 */
	protected $System = null;

	/**
	 * współrzędnia X zdarzenia
	 *
	 * @var int
	 */
	protected $X = null;

	/**
	 * Współrzędna Y zdarzenia
	 *
	 * @var int
	 */
	protected $Y = null;

	/**
	 * Kogo dotyczy
	 *
	 * @var int
	 */
	protected $userID = null;

	/**
	 * Przez kogo
	 *
	 * @var unknown_type
	 */
	protected $byUserID = null;

	/**
	 * timestamp
	 *
	 * @var int
	 */
	protected $time;

	/**
	 * Typ
	 *
	 * @var int
	 */
	protected $type;

	protected $userName = '';
	protected $byUserName = '';

	public $doSave = true;

	/**
	 * Konstruktor
	 *
	 * @param int $type
	 * @param int $userID
	 * @param int $byUserID
	 * @param int $System
	 * @param int $X
	 * @param int $Y
	 */
	public function __construct($type, $userProperties = null, $byUserProperties = null, $position = null) {

		$this->type = $type;

		if (! empty ( $userProperties )) {
			$this->userID = $userProperties->UserID;
			$this->userName = $userProperties->Name;
		}

		if (! empty ( $byUserProperties )) {
			$this->byUserID = $byUserProperties->UserID;
			$this->byUserName = $byUserProperties->Name;
		}

		if (! empty ( $position )) {
			$this->System = $position->System;
			$this->X = $position->X;
			$this->Y = $position->Y;
		}
		$this->time = time ();

	}

	/**
	 * Destruktor publiczny
	 *
	 */
	public function __destruct() {

		if ($this->doSave) {

			if ($this->System !== null) {
				$tSystem = "'" . $this->System . "'";
			} else {
				$tSystem = 'null';
			}

			if (! empty ( $this->userID )) {
				$userID = "'" . $this->userID . "'";
			} else {
				$userID = 'null';
			}
			if (! empty ( $this->byUserID )) {
				$byUserID = "'" . $this->byUserID . "'";
			} else {
				$byUserID = 'null';
			}

			$tQuery = "INSERT INTO newsagency(Text, Date, System, Type, UserID, ByUserID) VALUES('" . \Database\Controller::getInstance()->quote(serialize ( $this )) . "','{$this->time}',{$tSystem},'{$this->type}',{$userID},{$byUserID})";
			\Database\Controller::getInstance()->execute ( $tQuery );

			/**
			 * Wyczyść cache wszystkich zaleznych modułów
			 */
            //Check if it will work
            \phpCache\Factory::getInstance()->create()->clearClassCache ( 'newsAgency' );

		}
	}

	public function render($short = false) {

		$retVal = '';

		switch ($this->type) {

			/*
			 * Sygnał SOS
			*/
			case 1 :

				if ($short) {
					$retVal = '<div style="color: yellow">' . TranslateController::getDefault()->get ( 'MaydayAt' ) . ' ' . $this->System . '/' . $this->X . '/' . $this->Y . '</div>';
				} else {
					$retVal = '<tr style="color: yellow"><td>' . \General\Formater::formatDateTime ( $this->time ) . '</td><td>' . $this->userName . ' ' . TranslateController::getDefault()->get ( 'callsMaydayAt' ) . ' ' . $this->System . '/' . $this->X . '/' . $this->Y . '</td>';
				}
				break;
					
				/*
				 * Zniszczenie statku
				*/
			case 2 :

				if ($short) {
					$retVal = '<div style="color: red">' . TranslateController::getDefault()->get ( 'ShipDestroyedAt' ) . ' ' . $this->System . '/' . $this->X . '/' . $this->Y . '</div>';
				} else {
					$retVal = '<tr style="color: red"><td>' . \General\Formater::formatDateTime ( $this->time ) . '</td><td>' . $this->userName . ' ' . TranslateController::getDefault()->get ( 'wasDestroyedBy' ) . ' ' . $this->byUserName . ' ' . TranslateController::getDefault()->get ( 'atSector' ) . ' ' . $this->System . '/' . $this->X . '/' . $this->Y . '</div>';
				}
				break;

		}

		return $retVal;
	}

}
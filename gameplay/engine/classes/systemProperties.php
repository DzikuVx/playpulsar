<?php

/**
 * Klasa parametrów systemu
 * Używana tabela: systems
 * Parametr wejściowy: SystemID
 * @version $Rev: 455 $
 * @package Engine
 */
class systemProperties extends extendedItem {

	protected $tableName = "systems";
	protected $tableID = "SystemID";
	protected $tableUseFields = array('SystemID','Name','Width','Height','Number','Enabled','Galaxy','MapAvaible');
	protected $cacheExpire = 86400;

	public $SystemID;
	public $Name;
	public $Width;
	public $Height;
	public $Number;
	public $Enabled;
	public $Galaxy;
	public $MapAvaible;

    /**
     * Get random port in system, different that current position
     * @param \Gameplay\Model\ShipPosition $shipPosition
     * @return stdClass
     */
    static function randomPort(\Gameplay\Model\ShipPosition $shipPosition) {

		$retVal = new stdClass();

		$retVal->System = $shipPosition->System;

		if (empty ( $shipPosition->X )) {
			$shipPosition->X = 0;
		}
		if (empty ( $shipPosition->Y )) {
			$shipPosition->Y = 0;
		}

		$tQuery2 = "
          SELECT
            PortID,
            X,
            Y
          FROM
            ports
          WHERE
            System = '{$shipPosition->System}' AND
            X != '{$shipPosition->X}' AND
            Y != '{$shipPosition->Y}'
          ORDER BY
            RAND()
          LIMIT 1";
		$tQuery2 = \Database\Controller::getInstance()->execute ( $tQuery2 );
		while ( $tR2 = \Database\Controller::getInstance()->fetch ( $tQuery2 ) ) {
			$retVal->X = $tR2->X;
			$retVal->Y = $tR2->Y;
			$retVal->PortID = $tR2->PortID;
		}

		return $retVal;
	}

	/**
	 * Zwraca losową pozycją wewnątrz systemu
	 *
	 * @param int $ID - ID Systemy
	 * @return stdClass
	 */
	static public function randomPosition($ID) {

		$retVal = new stdClass();
		$retVal->X = null;
		$retVal->Y = null;
		$retVal->System = $ID;

		$tData = new self ($ID);

		$retVal->X = rand ( 1, $tData->Width );
		$retVal->Y = rand ( 1, $tData->Height );

		unset ( $tData );

		return $retVal;
	}

	/**
	 * Pobranie numeru galaktyki do której należy system
	 *
	 * @param int $systemID
	 * @return int
	 */
	static public function getGalaxy($systemID) {

		$item = new self($systemID);

		return $item->Galaxy;
	}

}

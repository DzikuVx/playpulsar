<?php

namespace Gameplay\Model;

class SystemProperties extends Standard {

	protected $tableName = "systems";
	protected $tableID = "SystemID";
	protected $tableUseFields = array('SystemID','Name','Width','Height','Number','Enabled','Galaxy','MapAvaible');
	protected $cacheExpire = 86400;

    /**
     * @var int
     */
    public $SystemID;

    /**
     * @var string
     */
    public $Name;

    /**
     * @var int
     */
    public $Width;

    /**
     * @var int
     */
    public $Height;

    /**
     * @var int
     */
    public $Number;

    /**
     * @var string
     */
    public $Enabled;

    /**
     * @var int
     */
    public $Galaxy;

    /**
     * @var string
     */
    public $MapAvaible;

    /**
     * Get random port in system, different that current position
     * @param ShipPosition $shipPosition
     * @return \stdClass
     */
    static function randomPort(ShipPosition $shipPosition) {

		$retVal = new \stdClass();

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
	 * @param int $ID
	 * @return \stdClass
	 */
	static public function randomPosition($ID) {

		$retVal = new \stdClass();
		$retVal->X = null;
		$retVal->Y = null;
		$retVal->System = $ID;

		$tData = new self ($ID);

		$retVal->X = rand ( 1, $tData->Width );
		$retVal->Y = rand ( 1, $tData->Height );

		return $retVal;
	}

	/**
	 * @param int $systemID
	 * @return int
	 */
	static public function getGalaxy($systemID) {

		$item = new self($systemID);

		return $item->Galaxy;
	}

}

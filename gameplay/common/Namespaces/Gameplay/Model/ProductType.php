<?php

namespace Gameplay\Model;

class ProductType extends Standard {
	protected $tableName = "products";
	protected $tableID = "ProductID";
	protected $tableUseFields = array('ProductID', 'NamePL', 'NameEN', 'Symbol', 'PriceMin', 'PriceMax', 'Experience', 'ExpMin', 'ExpMax', 'Size', 'RegularSell', 'RegularBuy', 'CreationDivider', 'Active');
	protected $cacheExpire = 86400;

    /**
     * @var int
     */
    public $ProductID;

    /**
     * @var string
     */
    public $NamePL;

    /**
     * @var string
     */
    public $NameEN;
    public $Symbol;
    public $PriceMin;
    public $PriceMax;
    public $Experience;
    public $ExpMin;
    public $ExpMax;
    public $Size;

    /**
     * @var string
     */
    public $RegularSell;

    /**
     * @var string
     */
    public $RegularBuy;

    /**
     * @var int
     */
    public $CreationDivider;

    /**
     * @var string
     */
    public $Active;

	/**
	 * Zwraca tablicę n losowych towarów
	 *
	 * @param int $count
	 * @return array
	 */
	static public function getRand($count) {

		$retVal = array ();

        $oDb = \Database\Controller::getInstance();

		$tQuery = "SELECT ProductID FROM products WHERE Active='yes' ORDER BY RAND() LIMIT " . $count;
		$tQuery = $oDb->execute ( $tQuery );
		while ( $resultRow = $oDb->fetch ( $tQuery ) ) {
			array_push($retVal, $resultRow->ProductID);
		}
		return $retVal;
	}

	/**
	 * Zwraca cenę towaru
	 *
	 * @param int $amount
	 * @return int
	 */
	public function getPrice($amount) {
		return self::computePrice($amount, $this->PriceMin, $this->PriceMax);
	}

	public function getExperienceForBuy($portAmount) {

		global $config;

		return round(($this->Experience / $config ['port'] ['maxCargoAmount']) * $portAmount);
	}

	public function getExperienceForSell($portAmount) {
		return $this->Experience - $this->getExperienceForBuy($portAmount);
	}

    /**
     * @param int $sectorAmount
     * @return int
     */
    public function getExperienceForGather(/** @noinspection PhpUnusedParameterInspection */
        $sectorAmount = 0) {
		return $this->Experience;
	}

    /**
     * @param int $sectorAmount
     * @return int
     */
    public function getExperienceForJettison(/** @noinspection PhpUnusedParameterInspection */
        $sectorAmount = 0) {
		return 0;
	}

    /**
     * @param int $amount
     * @param int $min
     * @param int $max
     * @return float|int
     */
    static public function computePrice($amount, $min, $max) {

		global $config;

		$out = 0;

		if ($amount <= $config ['port'] ['cargoThresholdLow']) {
			$out = $max;
		}

		if ($amount >= $config ['port'] ['cargoThresholdHigh']) {
			$out = $min;
		}

		if ($amount > $config ['port'] ['cargoThresholdLow'] && $amount < $config ['port'] ['cargoThresholdHigh']) {
			$temp_a = ($max - $min) / ($config ['port'] ['cargoThresholdLow'] - $config ['port'] ['cargoThresholdHigh']);
			$temp_b = $max - ((($max - $min) * $config ['port'] ['cargoThresholdLow']) / ($config ['port'] ['cargoThresholdLow'] - $config ['port'] ['cargoThresholdHigh']));
			$out = floor ( ($temp_a * $amount) + $temp_b );
		}

		return $out;
	}

	/**
	 * @param int $portID
	 * @param int $productID
	 * @param string $type
	 * @param string $mode
	 * @return int
	 */
	static public function sGetAmountInPort($portID, $productID, $type, $mode) {
        //FIXME replace with dynamic
        $oDb = \Database\Controller::getInstance();

		$out = 0;
		$tQuery = "SELECT
                Amount AS ile
            FROM
                portcargo
            WHERE
                PortID = '$portID' AND
                CargoID = '$productID' AND
                Type = '$type' AND
                Mode = '$mode'
            LIMIT 1";
		$tQuery = $oDb->execute($tQuery);
		while ($tR1 = $oDb->fetch($tQuery)) {
			$out = $tR1->ile;
		}

		return $out;
	}
}
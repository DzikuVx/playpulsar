<?php
/**
 * Towar handlowy
 *
 * @version $Rev: 454 $
 * @package Engine
 */
class product extends baseItem {
	protected $tableName = "products";
	protected $tableID = "ProductID";
	protected $tableUseFields = null;
	protected $defaultCacheExpire = 86400;
	protected $useMemcached = true;

	/**
	 * Szybkie pobranie danych towaru
	 *
	 * @param int $ID
	 * @return stdClass
	 */
	static public function quickLoad($ID, $useCache = true) {
		$item = new product ( );
		$retVal = $item->load ( $ID, $useCache, $useCache );
		unset($item);
		return $retVal;
	}

	/**
	 * Zwraca tablicę n losowych towarów
	 *
	 * @param int $count
	 * @return array
	 */
	static public function getRand($count) {

		$retVal = array ();

		$tQuery = "SELECT ProductID FROM products WHERE Active='yes' ORDER BY RAND() LIMIT " . $count;
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			array_push ( $retVal, $resultRow->ProductID );
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

		global $config;

		return self::computePrice($amount, $this->dataObject->PriceMin, $this->dataObject->PriceMax);
	}

	public function getExperienceForBuy($portAmount) {

		global $config;

		return round(($this->dataObject->Experience / $config ['port'] ['maxCargoAmount']) * $portAmount);
	}

	public function getExperienceForSell($portAmount) {
		global $config;

		return $this->dataObject->Experience - $this->getExperienceForBuy($portAmount);
	}

	public function getExperienceForGather($sectorAmount = 0) {

		$retVal = $this->dataObject->Experience;
		return $retVal;
	}

	public function getExperienceForJettison($sectorAmount = 0) {

		$retVal = 0;
		return $retVal;
	}



	/**
	 * Zwraca cenę towaru, wersja statyczna
	 *
	 * @param int $amount
	 * @return int
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
	 * Pobranie ilości towaru w porcie
	 *
	 * @param int $portID
	 * @param int $productID
	 * @param string $type
	 * @param string $mode
	 * @return int
	 */
	public function sGetAmountInPort($portID, $productID, $type, $mode) {

		//Sprawdz, czy port zawiera odpowiednia ilosc towaru ktory chcesz kupić
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
    ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$out = $tR1->ile;
		}

		return $out;
	}

}

<?php
class ship extends baseItem {
	protected $tableName = "shiptypes";
	protected $tableID = "ShipID";
	protected $tableUseFields = null;
	protected $defaultCacheExpire = 86400;
	protected $useMemcached = true;

	/**
	 * Szczegóły okrętu
	 *
	 * @param array $params
	 * @return string
	 */
	public function renderDetail($params) {

		$t = new translation ( $params [0], dirname ( __FILE__ ) . '/../translations.php' );

		$template = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/shipDetail.html', $t );

		$tObject = ship::quickLoad ( $params [1] );

		if ($params [0] == 'pl') {
			$tObject->Name = $tObject->NamePL;
		} else {
			$tObject->Name = $tObject->NameEN;
		}

		$template->add ( $tObject );

		return ( string ) $template;
	}

	/**
	 * Szybkie pobranie danych statku
	 *
	 * @param int $ID
	 * @return stdClass
	 */
	static public function quickLoad($ID, $useCache = true) {
		$item = new ship ( );
		$retVal = $item->load ( $ID, $useCache, $useCache );
		unset($item);
		return $retVal;
	}

}
?>
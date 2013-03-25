<?php
/**
 * Element wyposażenia
 * 
 * @version $Rev: 395 $
 * @package Engine
 */
class equipment extends baseItem {
	protected $tableName = "equipmenttypes";
	protected $tableID = "EquipmentID";
	protected $tableUseFields = null;
	protected $defaultCacheExpire = 86400;
	protected $useMemcached = true;

	/**
	 * Cena naprawy equipu
	 *
	 * @param int $equiapmentID
	 * @return int
	 */
	static public function sGetRepairPrice($equiapmentID) {

		global $config;

		$tData = equipment::quickLoad ( $equiapmentID );

		if (empty ( $tData->Price )) {
			return 0;
		}

		$retVal = ceil ( $tData->Price * $config ['equipment'] ['repairCost'] ['cash'] );

		return $retVal;
	}

	/**
	 * Szczegóły equipmnetu
	 *
	 * @param array $params - ID jako pierwszy element tablicy
	 * @return string
	 */
	public function renderDetail($params) {

		$t = new translation ( $params [0], dirname ( __FILE__ ) . '/../translations.php' );

		$template = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/equipmentDetail.html', $t );

		$tObject = self::quickLoad ( $params [1] );

		if ($params [0] == 'pl') {
			$tObject->Name = $tObject->NamePL;
		} else {
			$tObject->Name = $tObject->NameEN;
		}

		$template->add ( $tObject );

		return ( string ) $template;
	}

	/**
	 * Konstruktor statyczny
	 *
	 * @param int $ID
	 * @return stdClass
	 */
	static public function quickLoad($ID) {

		$item = new equipment ( );
		$retVal = $item->load ( $ID, true, true );
		unset($item);
		return $retVal;
	}

}
?>
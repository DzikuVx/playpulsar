<?php
/**
 * Klasa broni
 *
 * @version $Rev: 455 $
 * @package Engine
 */
class weapon extends baseItem {
	protected $tableName = "weapontypes";
	protected $tableID = "WeaponID";
	protected $tableUseFields = null;
	protected $defaultCacheExpire = 86400;
	protected $useMemcached = true;

	/**
	 * Pobranie ceny przeładowania uzbrojenia
	 *
	 * @param int $weaponID
	 * @param int $currentAmmo
	 * @return int
	 */
	static public function sGetReloadPrice($weaponID, $currentAmmo) {

		$tData = weapon::quickLoad ( $weaponID );

		if (empty ( $tData->Ammo )) {
			return 0;
		}

		$retVal = ceil ( ($tData->Price - ($tData->Price * ($currentAmmo / $tData->Ammo))) * 0.6 );

		return $retVal;
	}

	/**
	 * Pobranie ceny naprawy broni
	 *
	 * @param int $weaponID
	 * @return int
	 */
	static public function sGetRepairPrice($weaponID) {

		global $config;

		$tData = weapon::quickLoad ( $weaponID );

		if (empty ( $tData->Price )) {
			return 0;
		}

		$retVal = ceil ( $tData->Price * $config ['weapon'] ['repairCost'] ['cash'] );

		return $retVal;
	}

	/**
	 * Szczegóły broni
	 *
	 * @param array $params - język jako pierwszy, ID jako drugi element tablicy
	 * @return string
	 */
	public function renderDetail($params) {

		$t = new translation ( $params [0], dirname ( __FILE__ ) . '/../translations.php' );

		$template = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/weaponDetail.html', $t );

		$tObject = weapon::quickLoad ( $params [1] );

		if ($params [0] == 'pl') {
			$tObject->Name = $tObject->NamePL;
			$tObject->ClassName = $tObject->ClassNamePL;
		} else {
			$tObject->Name = $tObject->NameEN;
			$tObject->ClassName = $tObject->ClassNameEN;
		}

		if ($tObject->Ammo === null) {
			$tObject->Ammo = '-';
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
	static public function quickLoad($ID, $useCache = true) {
		$item = new weapon ( );
		$retVal = $item->load ( $ID, $useCache, $useCache );
		unset($item);
		return $retVal;
	}

	/**
	 * Pobranie elementu z bazy danych
	 *
	 * @param int $ID
	 * @return boolean
	 */
	function get($ID) {

		$this->dataObject = null;

		$tResult = \Database\Controller::getInstance()->execute ( "
      SELECT 
        weapontypes.*,
        weaponclasses.NamePL AS ClassNamePL,
        weaponclasses.NameEN As ClassNameEN
      FROM 
        weapontypes JOIN weaponclasses USING(WeaponClassID)
      WHERE 
        WeaponID='$ID'
      LIMIT
        1
      " );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tResult ) ) {
			$this->dataObject = $resultRow;
		}
		$this->ID = $this->parseCacheID ( $ID );
		return true;
	}

}
<?php
/**
 * routing
 *
 * @version $Rev: 454 $
 * @package Engine
 */
class shipRouting extends baseItem {
	protected $tableName = "shiprouting";
	protected $tableID = "UserID";
	protected $tableUseFields = array ('System', 'X', 'Y' );
	protected $defaultCacheExpire = 1800;
	protected $useMemcached = true;

	/**
	 * Reset współrzędnych
	 *
	 * @param stdClass $routing
	 */
	private static function reset($routing) {
		$routing->System = null;
		$routing->X = null;
		$routing->Y = null;
	}

	/**
	 * Funkcja sprawdzająca, czy statek przybył na miejsce
	 *
	 * @param stdClass $position
	 * @param stdClass $routing
	 */
	static public function checkArrive($position, $routing) {
		if ($position->System == $routing->System && $position->X == $routing->X && $position->Y == $routing->Y) {
			self::reset ( $routing );
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Pobranie pozycji statku z bazy danych
	 *
	 * @param int $ID - ID gracza
	 * @return true
	 */
	function get($ID) {

		$this->dataObject = new stdClass();

		$tResult = \Database\Controller::getInstance()->execute ( "SELECT shiprouting.UserID, shiprouting.System, shiprouting.X, shiprouting.Y FROM shiprouting WHERE shiprouting.UserID='$ID'" );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tResult ) ) {
			$this->dataObject = $resultRow;
		}
		$this->ID = $ID;

		if (empty($this->dataObject->UserID)) {
			$this->dataObject->UserID = $ID;
			$this->dataObject->System = null;
			$this->dataObject->X = null;
			$this->dataObject->Y = null;
		}

		return true;
	}

	/* Zapisanie do bazy danych
	 *
	 * @param stdClass $object
	 * @return boolean
	 */
	public function set($object) {

		if ($object->System == null) {
			\Database\Controller::getInstance()->execute ( "DELETE FROM shiprouting WHERE UserID = '{$this->ID}'" );
		} else {
			if ($this->dataObject->System != null) {
				\Database\Controller::getInstance()->execute ( $this->formatUpdateQuery ( $object ) );
			} else {
				\Database\Controller::getInstance()->execute ( $this->formatInsertQuery ( $object ) );
			}
		}
		return true;
	}

}
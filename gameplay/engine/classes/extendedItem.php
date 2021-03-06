<?php

/**
 * Klasa bazowa dla elementów
 *
 * @version $Rev: 377 $
 * @package Engine
 */
abstract class extendedItem {
	protected $ID = null;

	protected $cache = null;

	/**
	 * Oryginalne dane
	 * @var stdClass
	 */
	protected $originalData = null;

	protected $tableName = "";
	protected $tableID = "";
	protected $tableUseFields = "";
	protected $cacheExpire = 3600;

	protected $useCache = true;

	protected $dbID = null;
	protected $cacheID = null;

	/**
	 * Wstawienie pozycji do bazy danych
	 *
	 * @param stdClass $data
	 * @return int - id wstawionej pozycji
	 */
	protected function insert() {

		$tQuery = $this->formatInsertQuery ( );
		\Database\Controller::getInstance()->execute ( $tQuery );

		return \Database\Controller::getInstance()->lastUsedID ();
	}

	/**
	 * Konstruktor klasy bazowej
	 *
	 * @param int $ID
	 * @param boolean $useCache
	 * @param dataBase $db
	 * @param mixed $cache
	 */
	function __construct($ID = null, $useCache = true, $db = null, $cache = null) {

		$this->cache = $cache;

		if (empty($this->cache)) {
			$this->cache = \Cache\Controller::getInstance();
		}

		$this->ID = $ID;

		$this->useCache = $useCache;

		$this->cacheID = $this->parseCacheID($ID);
		$this->dbID = $this->parseDbID($ID);

		if (!empty($this->ID)) {
			$this->load();
		}

	}

	/**
	 * Pobranie obiektu z cache
	 *
	 * @param int $ID - ID obiektu do pobrania
	 * @throws Exception
	 * @return boolean
	 */
	protected function fromCache() {

		if (empty($this->cacheID)) {
			throw new Exception('Cache ID empty, load failed');
		}
		//@FIXME cache identifier have to come from table name not class name
		if ($this->cache->check ( get_class ( $this ), $this->cacheID )) {
			$this->loadData( $this->cache->get ( get_class ( $this ), $this->cacheID ), true );
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Zapis obiektu do cache
	 *
	 * @return boolean
	 */
	protected function toCache() {

		if (empty($this->cacheID)) {
			throw new Exception('Cache ID empty, save failed');
		}

		$this->cache->set ( get_class ( $this ), $this->cacheID, $this->serializeData(), $this->cacheExpire );

		return true;
	}

	public function clearCache() {
		$this->cache->clear(get_class ( $this ), $this->ID);
	}

	protected function loadData($data, $serialize) {

		if (!empty($serialize)) {
			$data = unserialize($data);
		}

		$this->originalData = $data;

		foreach ($this->tableUseFields as $tField) {
			if (isset($data->{$tField})) {
				$this->{$tField} = $data->{$tField};
			}
		}

		return true;
	}

	protected function serializeData() {
		$retVal = new stdClass();

		foreach ($this->tableUseFields as $tField) {
			if (isset($this->{$tField})) {
				$retVal->{$tField} = $this->{$tField};
			}
		}

		return serialize($retVal);
	}

	/**
	 * Funkcja parsująca obiekt do zapytania typu UPDATE do bazy danych
	 *
	 * @return string
	 */
	protected function formatUpdateQuery() {

		$retVal = "UPDATE " . $this->tableName . " SET ";

		$tStr = "";
		foreach ( $this as $key => $value ) {

			if (in_array ( $key, $this->tableUseFields ) && $value != $this->originalData->{$key}) {

				if ($value != null) {
					$tStr .= "," . $key . "='" . \Database\Controller::getInstance()->quote($value) . "'";
				} else {
					$tStr .= "," . $key . "=null ";
				}

			}

		}

		if ($tStr [0] == ",")
		$tStr = substr ( $tStr, 1 );

		$retVal .= $tStr;

		unset ( $tStr );

		$retVal .= " WHERE {$this->tableID}='" . \Database\Controller::getInstance()->quote($this->dbID) . "' LIMIT 1";

		return $retVal;
	}

	/**
	 * Złożenie zapytania do INSERT
	 *
	 * @return string
	 */
	protected function formatInsertQuery() {

		$retVal = "INSERT INTO " . $this->tableName . "(";

		$tStr = "";

		foreach ( $this as $key => $value ) {
			if (in_array ( $key, $this->tableUseFields ) || $key == $this->tableID) {

				$tStr .= "," . $key;

			}
		}

		if ($tStr [0] == ",")
		$tStr = substr ( $tStr, 1 );

		$retVal .= $tStr;

		$retVal .= ") VALUES(";

		$tStr = "";
		foreach ( $this as $key => $value ) {
			if (in_array ( $key, $this->tableUseFields ) || $key == $this->tableID) {
				if ($value !== null) {
					$tStr .= ",'" . \Database\Controller::getInstance()->quote($value) . "'";
				} else {
					$tStr .= ",null";
				}
			}
		}

		if ($tStr [0] == ",")
		$tStr = substr ( $tStr, 1 );

		$retVal .= $tStr;

		unset ( $tStr );

		$retVal .= ")";

		return $retVal;
	}

	protected function checkIfChanged() {
		$retVal = false;

		foreach ($this->tableUseFields as $tField) {

			if ($this->{$tField} != $this->originalData->{$tField}) {
				$retVal = true;
				break;
			}

		}

		return $retVal;
	}

	/**
	 * Synchronizacja z cache i bazą danych
	 */
	public function synchronize() {

		if ($this->checkIfChanged()) {
			$this->set();
			$this->toCache();
			return true;
		}else {
			return false;
		}
	}

	/**
	 * Zapisanie do bazy danych
	 */
	protected function set() {

		if (empty($this->dbID)) {
			throw new Exception('Object not initialized properly');
		}

		\Database\Controller::getInstance()->execute ( $this->formatUpdateQuery ( ) );
	}

	/**
	 * Zwraca sparsowany klucz dla cache klasy
	 *
	 * @param int $ID
	 * @return int
	 */
	protected function parseCacheID($ID) {

		return $ID;
	}

	/**
	 * Zwraca sparsowany klucz dla bazy danych
	 *
	 * @param int $ID
	 * @return int
	 */
	protected function parseDbID($ID) {

		return $ID;
	}

	/**
	 * Ładuje obiekt z bazy danych lub cache
	 *
	 * @param $object
	 * @param boolean $useCache
	 * @param boolean $useSession
	 * @return stdClass
	 */
	protected function load() {

		if ($this->useCache) {

			if (! $this->fromCache()) {
				$this->get ( );
				$this->toCache ( );
			}
		} else {
			$this->get ( );
		}
	}

	/**
	 * Przeładowanie obiektu wraz z synchronizacją do bazy danych i cache
	 *
	 * @param int $newID
	 * @param mixed $object
	 * @param boolean $useCache
	 * @param boolean $useSession
	 * @return strClass
	 */
	public function reload($newID) {

		if (!empty($this->ID)) {
			$this->synchronize (  );
		}

		$this->ID = $newID;
		$this->cacheID = $this->parseCacheID($newID);
		$this->dbID = $this->parseDbID($newID);
		$this->load( );
	}

	/**
	 * Pobranie elementu z bazy danych
	 *
	 * @param int $ID
	 * @return boolean
	 */
	protected function get() {

		if (empty($this->dbID)) {
			throw new Exception('Object not initialized properly');
		}

		$tResult = \Database\Controller::getInstance()->execute ( "
      SELECT
        *
      FROM
      {$this->tableName}
      WHERE
      {$this->tableID}='{$this->dbID}'
      LIMIT
        1" );
      while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tResult ) ) {
      	$this->loadData($resultRow, false);
      }
      return true;
	}

	/**
	 * Szybkie pobranie parametrów systemu
	 *
	 * @param int $ID
	 * @param boolean $useCache
	 * @return mixed
	 */
	static public function quickLoad($ID, $useCache = true) {
		$item = new static ($ID );
		return $item;
	}

	/**
	 * Szybkie wstawienie
	 *
	 * @param stdClass $data
	 * @return boolean
	 */
	static public function quickInsert($data) {

		$item = new static ( );
		$retVal = $item->insert ( $data );
		unset($item);

		return $retVal;
	}

}
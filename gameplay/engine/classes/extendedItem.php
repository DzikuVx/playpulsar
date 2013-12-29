<?php

abstract class extendedItem {
	protected $ID = null;

	protected $cache = null;

	/**
	 * @var stdClass
	 */
	protected $originalData = null;

	protected $tableName = "";
	protected $tableID = "";

    /**
     * @var array
     */
    protected $tableUseFields = array();

	protected $cacheExpire = 3600;

	protected $useCache = true;

	protected $dbID = null;
	protected $cacheID = null;

    static public function sFlushCache($id) {
        $oObject = new static($id);
        /** @noinspection PhpUndefinedMethodInspection */
        $oObject->clearCache();
    }

	protected function insert() {

		$tQuery = $this->formatInsertQuery ( );
		\Database\Controller::getInstance()->execute ( $tQuery );

		return \Database\Controller::getInstance()->lastUsedID ();
	}

    //FIXME remove $db and $cache
    /**
     * @param null $ID
     * @param bool $useCache
     * @param null $db
     * @param null $cache
     */
    function __construct($ID = null, $useCache = true, $db = null, $cache = null) {

		$this->cache = $cache;

		if (empty($this->cache)) {
			$this->cache = \phpCache\Factory::getInstance()->create();
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
     * @return bool
     * @throws Exception
     */
    protected function fromCache() {

		if (empty($this->cacheID)) {
			throw new Exception('Cache ID empty, load failed');
		}
		//@FIXME cache identifier have to come from table name not class name
		
		$oCacheKey = new \phpCache\CacheKey($this, $this->cacheID);
		
		if ($this->cache->check ( $oCacheKey )) {
			$this->loadData( $this->cache->get ( $oCacheKey ), true );
			return true;
		} else {
			return false;
		}

	}

	protected function toCache() {

		if (empty($this->cacheID)) {
			throw new Exception('Cache ID empty, save failed');
		}

		//@FIXME make $oCacheKey a global class identifier
		$oCacheKey = new \phpCache\CacheKey($this, $this->cacheID);
		
		$this->cache->set ( $oCacheKey, $this->serializeData(), $this->cacheExpire );

		return true;
	}

	public function clearCache() {
		//@FIXME make $oCacheKey a global class identifier
		$oCacheKey = new \phpCache\CacheKey($this, $this->cacheID);
		
		$this->cache->clear($oCacheKey);
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

    protected function load() {

		if ($this->useCache) {

			if (!$this->fromCache()) {
				$this->get( );
				$this->toCache( );
			}
		} else {
			$this->get();
		}
	}

	public function reload($newID) {

		if (!empty($this->ID)) {
			$this->synchronize (  );
		}

		$this->ID = $newID;
		$this->cacheID = $this->parseCacheID($newID);
		$this->dbID = $this->parseDbID($newID);
		$this->load( );
	}

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
	 * @param int $ID
	 * @param bool $useCache
	 * @return mixed
	 */
	static public function quickLoad($ID, /** @noinspection PhpUnusedParameterInspection */
                                     $useCache = true) {
		$item = new static ($ID );
		return $item;
	}

    /**
     * @param $data
     * @return mixed
     */
    static public function quickInsert($data) {

        $item = new static();
        /** @noinspection PhpUndefinedMethodInspection */
        $retVal = $item->insert ( $data );

		return $retVal;
	}

}
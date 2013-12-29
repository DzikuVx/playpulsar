<?php
abstract class baseItem {
	protected $ID = null;

	//@todo !quotowanie!

    /**
     * @var stdClass
     */
    protected $dataObject = null;
	protected $language;
	protected $tableName = "";
	protected $tableID = "";
	protected $tableUseFields = "";

	protected $retVal = "";
	protected $defaultCacheExpire = 60;
	protected $useMemcached = false;

    static public function sFlushCache($id) {
        $oObject = new static($id);
        /** @noinspection PhpUndefinedMethodInspection */
        $oObject->clearCache();
    }

	/**
	 * @param stdClass $data
	 * @return int
	 */
	public function insert($data) {

		$tQuery = $this->formatInsertQuery ( $data );
		\Database\Controller::getInstance()->execute ( $tQuery );

		return \Database\Controller::getInstance()->lastUsedID ();
	}

	/**
	 * @param int $ID
	 * @param string $defaultAction
	 */
	function __construct($ID = null, $defaultAction = null) {

		$this->ID = $ID;

		if ($this->ID != null) {
		    $this->get( $ID );
        }

		if ($defaultAction != null) {
			$this->{$defaultAction};
		}
	}

	/**
	 *
	 * @param int $ID
	 * @return boolean
	 */
	function fromCache($ID) {

		$oCacheKey = new \phpCache\CacheKey($this, $ID);
        $oCache    = \phpCache\Factory::getInstance()->create();

		if ($oCache->check( $oCacheKey )) {
			$this->ID = $ID;
			$this->dataObject = $this->toObject($oCache->get ( $oCacheKey ) );
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param boolean $useSession - czy wykorzystywać zapis w sesji
	 * @return boolean
	 */
	function toCache(/** @noinspection PhpUnusedParameterInspection */
        $useSession = false) {

		$oCacheKey = new \phpCache\CacheKey($this, $this->ID);
        \phpCache\Factory::getInstance()->create()->set ( $oCacheKey, $this->toArray (), $this->defaultCacheExpire );
		
		return true;
	}

	public function clearCache() {
        \phpCache\Factory::getInstance()->create()->clear(new \phpCache\CacheKey($this, $this->ID));
	}

	/**
	 * @return array
	 */
	final protected function toArray() {

		if ($this->dataObject == null) {
			return false;
		}
			
		$retVal = null;

		foreach ( $this->dataObject as $key => $value ) {
			$retVal [$key] = $value;
		}
		return $retVal;
	}

	/**
	 * Przekaształca tablicę na obiekt
	 *
	 * @param array $array
	 * @return object
	 */
	final protected function toObject($array) {

		$retVal = new stdClass();

		if ($array == null)
		return null;
			
		foreach ( $array as $key => $value ) {
			$retVal->{$key} = $value;
		}
		return $retVal;
	}

	/**
	 * Zwraca dataObject
	 *
	 * @return stdClass
	 */
	function give() {

		if ($this->dataObject == null) {
			return null;
		} else {
			return clone $this->dataObject;
		}
	}

	public function getDataObject() {
		return $this->dataObject;
	}

	/**
	 * Funkcja parsująca obiekt do zapytania typu UPDATE do bazy danych
	 *
	 * @param stdClass $object
	 * @param int $ID - wymuszona nazwa identyfikatora klucza głównego
	 * @return string
	 */
	protected function formatUpdateQuery($object, $ID = null) {

		if ($ID == null) {
			$ID = $this->dataObject->{$this->tableID};
		}
		$retVal = "UPDATE " . $this->tableName . " SET ";

		$tFieldCount = 0;

		$tStr = "";
		foreach ( $object as $key => $value ) {
			if (in_array ( $key, $this->tableUseFields ) && $value != $this->dataObject->{$key}) {
				$tFieldCount++;
				if ($value != null) {
					$tStr .= "," . $key . "='" . $value . "'";
				} else {
					$tStr .= "," . $key . "=null ";
				}
			}
		}

		if (empty($tFieldCount)) {
			return '';
		}

		if (isset($tStr [0]) && $tStr [0] == ",") {
			$tStr = substr ( $tStr, 1 );
		}

		$retVal .= $tStr;

		unset ( $tStr );

		$retVal .= " WHERE " . $this->tableID . "='" . $ID . "'";

		return $retVal;
	}

    /**
     * @param stdClass $object
     * @return string
     */
    protected function formatInsertQuery($object) {

		$retVal = "INSERT INTO " . $this->tableName . "(";

		$tStr = "";
		foreach ( $object as $key => $value ) {
			if (in_array ( $key, $this->tableUseFields ) || $key == $this->tableID) {

				$tStr .= "," . $key;

			}
		}

		if ($tStr [0] == ",")
		$tStr = substr ( $tStr, 1 );

		$retVal .= $tStr;

		$retVal .= ") VALUES(";

		$tStr = "";
		foreach ( $object as $key => $value ) {
			if (in_array ( $key, $this->tableUseFields ) || $key == $this->tableID) {
				if ($value !== null) {
					$tStr .= ",'" . $value . "'";
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

	/**
	 *
	 * @param stdClass $object
	 * @param boolean $useCache
	 * @param boolean $useSession
	 * @return boolean
	 */
	public function synchronize($object, $useCache = false, $useSession = false) {

		if ($this->dataObject != $object) {
			$this->set ( $object );
			$this->dataObject = $object;

			if ($useCache) {
				$this->toCache ( $useSession );
			}
		}

		return true;
	}

	/**
	 * Zapisanie do bazy danych
	 *
	 * @param stdClass $object
	 * @return boolean
	 */
	public function set($object) {

		if ($this->ID == null)
		return false;

		$tQuery = $this->formatUpdateQuery ( $object );

		if (!empty($tQuery)) {
			\Database\Controller::getInstance()->execute ( $tQuery );
		}
		return true;
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
	 * @param $object
	 * @param boolean $useCache
	 * @param boolean $useSession
	 * @return stdClass
	 */
	public function load($object, $useCache = false, $useSession = false) {

		$retVal = null;
		if ($useCache) {

			if (! $this->fromCache ( $this->parseCacheID ( $object ) )) {
				$this->get ( $object );
				$this->toCache ( $useSession );
			}

		} else {
			$this->get ( $object );
		}
		$retVal = $this->give ();
		return $retVal;
	}

	/**
	 *
	 * @param int $newID
	 * @param mixed $object
	 * @param boolean $useCache
	 * @param boolean $useSession
	 * @return stdClass
	 */
	public function reload($newID, $object, $useCache = false, $useSession = false) {

		$this->synchronize ( $object, $useCache, $useSession );
		return $this->load ( $newID, $useCache, $useSession );
	}

	/**
	 * @param int $ID
	 * @return boolean
	 */
	function get($ID) {

		$this->dataObject = null;

		$tResult = \Database\Controller::getInstance()->execute ( "
              SELECT
                *
              FROM
                {$this->tableName}
              WHERE
              {$this->tableID}='$ID'
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
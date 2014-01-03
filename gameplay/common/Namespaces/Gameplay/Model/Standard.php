<?php

namespace Gameplay\Model;

use Gameplay\Exception\Model;

abstract class Standard {

    /**
     * @var mixed
     */
    protected $entryId = null;

    /**
     * @var \phpCache\Apc
     */
    protected $cache = null;

    /**
     * @var \Database\MySQLiWrapper
     */
    protected $db;

    /**
     * @var \phpCache\CacheKey
     */
    protected $cacheKey;

    /**
     * @var \stdClass
     */
    protected $originalData = null;

    /**
     * @var string
     */
    protected $tableName = "";

    /**
     * @var mixed
     */
    protected $tableID = "";

    /**
     * @var array
     */
    protected $tableUseFields = array();

    /**
     * @var int
     */
    protected $cacheExpire = 3600;

    /**
     * @var bool
     */
    protected $useCache = true;

    /**
     * @var mixed
     */
    protected $dbID = null;

    /**
     * @var string
     */
    protected $cacheID = null;

    static public function sFlushCache($id) {
        $oObject = new static($id);
        /** @noinspection PhpUndefinedMethodInspection */
        $oObject->clearCache();
    }

    protected function insert() {

        $tQuery = $this->formatInsertQuery();
        $this->db->execute($tQuery);

        return $this->db->lastUsedID();
    }

    /**
     * Clear cache for all PortEntities
     */
    public function flushCacheModule() {
        $this->cache->clearModule(new \phpCache\CacheKey($this->tableName));
    }

    /**
     * @param mixed $entryId
     * @param bool $useCache
     */
    function __construct($entryId = null, $useCache = true) {

        $this->cache = \phpCache\Factory::getInstance()->create();
        $this->db    = \Database\Controller::getInstance();

        $this->entryId  = $entryId;
        $this->useCache = $useCache;

        if (!empty($this->entryId)) {
            $this->cacheID  = $this->parseCacheID($this->entryId);
            $this->dbID     = $this->parseDbID($this->entryId);
            $this->cacheKey = new \phpCache\CacheKey($this->tableName, $this->cacheID);

            $this->load();
        }
    }

    /**
     * @return bool
     * @throws Model
     */
    protected function fromCache() {

        if (empty($this->cacheID)) {
            throw new Model('Cache ID empty, load failed');
        }

        if ($this->cache->check($this->cacheKey)) {
            $this->loadData($this->cache->get($this->cacheKey), true );
            return true;
        } else {
            return false;
        }
    }

    protected function toCache() {
        if (empty($this->cacheID)) {
            throw new Exception('Cache ID empty, save failed');
        }

        $this->cache->set($this->cacheKey, $this->serializeData(), $this->cacheExpire);
        return true;
    }

    public function clearCache() {
        $this->cache->clear($this->cacheKey);
    }

    /**
     * @param \stdClass $data
     * @param bool $serialize
     * @return bool
     */
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

    /**
     * @return string
     */
    protected function serializeData() {
        $retVal = new \stdClass();

        foreach ($this->tableUseFields as $tField) {
            if (isset($this->{$tField})) {
                $retVal->{$tField} = $this->{$tField};
            }
        }

        return serialize($retVal);
    }

    /**
     * @return string
     * @throws \Gameplay\Exception\Model
     */
    protected function formatUpdateQuery() {

        $retVal = "UPDATE " . $this->tableName . " SET ";

        $tStr = "";
        foreach($this as $key => $value) {
            if (in_array( $key, $this->tableUseFields) && $value != $this->originalData->{$key}) {

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

    /**
     * @return bool
     */
    protected function checkIfChanged() {
        $retVal = false;

        foreach ($this->tableUseFields as $tField) {
            if ($this->originalData && property_exists($this->originalData, $tField) && $this->{$tField} != $this->originalData->{$tField}) {
                $retVal = true;
                break;
            }
        }

        return $retVal;
    }

    /**
     * Synchronize object with database and cache
     * @return bool
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
     * @throws \Gameplay\Exception\Model
     */
    protected function set() {

        if (empty($this->dbID)) {
            throw new Model('Object not initialized properly');
        }

        $this->db->execute($this->formatUpdateQuery());
    }

    /**
     * Method returns cache id based of entryId
     *
     * @param int $ID
     * @return mixed
     */
    protected function parseCacheID($ID) {

        return $ID;
    }

    /**
     * Method returns database id based of entryId
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
                $this->get();
                $this->toCache();
            }
        } else {
            $this->get();
        }
    }

    /**
     * Method clears all data stored within object
     */
    public function clear() {

        if (!empty($this->originalData)) {
            foreach($this->originalData as $sKey => $mValue) {
                if (property_exists($this, $sKey)) {
                    $this->{$sKey} = null;
                }
            }
        }

        if (!empty($this->tableUseFields)) {
            foreach($this->tableUseFields as $sKey) {
                if (property_exists($this, $sKey)) {
                    $this->{$sKey} = null;
                }
            }
        }

    }

    /**
     * Method reloads entry with new entryId
     * @param int $newID
     */
    public function reload($newID) {

        if (!empty($this->entryId)) {
            $this->synchronize();
        }

        $this->clear();

        $this->entryId  = $newID;
        $this->cacheID  = $this->parseCacheID($newID);
        $this->dbID     = $this->parseDbID($newID);
        $this->cacheKey = new \phpCache\CacheKey($this->tableName, $this->cacheID);

        $this->load();
    }

    /**
     * Method load entry from database
     * @return bool
     * @throws Model
     */
    protected function get() {

        if (empty($this->dbID)) {
            throw new Model('Object not initialized properly');
        }

        $tResult = $this->db->execute ("
            SELECT
                *
            FROM
                {$this->tableName}
            WHERE
                {$this->tableID}='{$this->dbID}'
            LIMIT
            1");

        while($resultRow = $this->db->fetch($tResult)) {
            $this->loadData($resultRow, false);
        }

        return true;
    }

    /**
     * @param int $ID
     * @param bool $useCache
     * @return mixed
     */
    static public function quickLoad($ID, $useCache = true) {
        $item = new static ($ID, $useCache);
        return $item;
    }

    /**
     * @param $data
     * @return mixed
     */
    static public function quickInsert($data) {
        $item = new static();
        /** @noinspection PhpUndefinedMethodInspection */
        return $item->insert($data);
    }

    /**
     * @return mixed
     */
    public function getEntryId() {
        return $this->entryId;
    }

} 
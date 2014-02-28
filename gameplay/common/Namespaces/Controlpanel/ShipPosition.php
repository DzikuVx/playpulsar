<?php

namespace Controlpanel;

class ShipPosition extends \Gameplay\Model\ShipPosition{

    /**
     * @param \user $user
     * @param $params
     * @return string
     */
    public function edit(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {
		$retVal = '';

		$this->entryId  = $params['id'];
		$this->useCache = false;
		$this->cacheID  = $this->parseCacheID($params['id']);
		$this->dbID     = $this->parseDbID($params['id']);
		$this->cacheKey = new \phpCache\CacheKey($this->getCacheModule(), $this->cacheID);

		$this->load();

		$retVal .= BaseItem::sRenderEditForm($this, $this, $params['id']);

		return $retVal;
	}

    /**
     * @param \user $user
     * @param array $params
     * @return string
     * @throws \customException
     */
    public function editExe(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {
		$retVal = '';

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$tQuery = BaseItem::sMakeUpdateQuery($this->tableName, $this->tableID, $this->tableUseFields, $params);

		$this->db->execute($tQuery);

        ShipPosition::sFlushCache($params['id']);

		\General\Controls::reloadWithMessage(\General\Session::get('returnLink'), "Data has been <strong>set</strong>", 'success');

		return $retVal;
	}
}
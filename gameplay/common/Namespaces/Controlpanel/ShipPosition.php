<?php

namespace Controlpanel;

class ShipPosition extends \shipPosition{
	
	public function edit($user, $params) {
		$retVal = '';

		$this->ID = $params['id'];
		$this->useCache = false;
		$this->cacheID = $this->parseCacheID($params['id']);
		$this->dbID = $this->parseDbID($params['id']);
		
		$this->load($params['id'], true, true);

		$retVal .= BaseItem::sRenderEditForm($this, $this, $params['id']);

		return $retVal;
	}

	public function editExe($user, $params) {
		$retVal = '';

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$tQuery = BaseItem::sMakeUpdateQuery($this->tableName, $this->tableID, $this->tableUseFields, $params);

		\Database\Controller::getInstance()->execute($tQuery);
        //FIXME will not work
		\phpCache\Factory::getInstance()->create()->clear('shipPosition', $params['id']);
		\General\Controls::reloadWithMessage(\General\Session::get('returnLink'), "Data has been <strong>set</strong>", 'success');

		return $retVal;
	}
}
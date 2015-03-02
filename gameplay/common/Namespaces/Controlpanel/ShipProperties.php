<?php

namespace Controlpanel;

class ShipProperties extends \Gameplay\Model\ShipProperties {
	
	public function edit(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {
		$retVal = '';

        $this->reload($params['id']);

		$retVal .= BaseItem::sRenderEditForm($this, $this, $params['id']);

		return $retVal;
	}

	public function editExe(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {
		$retVal = '';

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$tQuery = BaseItem::sMakeUpdateQuery($this->tableName, $this->tableID, $this->tableUseFields, $params);

		\Database\Controller::getInstance()->execute($tQuery);

		self::sQuickRecompute($params['id']);

		\General\Controls::reloadWithMessage(\General\Session::get('returnLink'), "Data has been <strong>set</strong>", 'success');
		
		return $retVal;
	}
}
<?php

namespace Controlpanel;

class UserStats extends \userStats {

	public function edit($user, $params) {
		$retVal = '';

		$data = $this->load($params['id'], true, true);

		$retVal .= \Controlpanel\BaseItem::sRenderEditForm($this, $data, $params['id']);

		return $retVal;
	}

	public function editExe($user, $params) {
		$retVal = '';

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$tQuery = \Controlpanel\BaseItem::sMakeUpdateQuery($this->tableName, $this->tableID, $this->tableUseFields, $params);

		\Database\Controller::getInstance()->execute($tQuery);

		\Cache\Controller::getInstance()->clear('userStats', $params['id']);

		$retVal .= \General\Controls::sUiDialog( "Confirmation", "Data has been <strong>set</strong>", "document.location='{$_SESSION['returnLink']}'");

		return $retVal;
	}

}
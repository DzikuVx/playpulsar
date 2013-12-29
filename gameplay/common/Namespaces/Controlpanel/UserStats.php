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

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$tQuery = \Controlpanel\BaseItem::sMakeUpdateQuery($this->tableName, $this->tableID, $this->tableUseFields, $params);

		\Database\Controller::getInstance()->execute($tQuery);
        \userStats::sFlushCache($params['id']);
		\General\Controls::reloadWithMessage(\General\Session::get('returnLink'), "Data has been <strong>set</strong>", 'success');
	}

}
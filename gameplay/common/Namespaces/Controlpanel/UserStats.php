<?php

namespace Controlpanel;

use Gameplay\Model\UserStatistics;

class UserStats extends UserStatistics {

	public function edit($user, $params) {
		$this->reload($params['id']);
		return \Controlpanel\BaseItem::sRenderEditForm($this, $this, $params['id']);
	}

	public function editExe($user, $params) {

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$tQuery = \Controlpanel\BaseItem::sMakeUpdateQuery($this->tableName, $this->tableID, $this->tableUseFields, $params);

		\Database\Controller::getInstance()->execute($tQuery);
        UserStatistics::sFlushCache($params['id']);
		\General\Controls::reloadWithMessage(\General\Session::get('returnLink'), "Data has been <strong>set</strong>", 'success');
	}

}
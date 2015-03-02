<?php

namespace Controlpanel;

use Gameplay\Model\UserEntity;

class UserProperties extends UserEntity {
	
	public function edit($user, $params) {
        $this->reload($params['id']);
        return BaseItem::sRenderEditForm($this, $this, $params['id']);
	}

	public function editExe($user, $params) {

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$tQuery = BaseItem::sMakeUpdateQuery($this->tableName, $this->tableID, $this->tableUseFields, $params);

		\Database\Controller::getInstance()->execute($tQuery);
		$this->clearCache();

		\General\Controls::reloadWithMessage(\General\Session::get('returnLink'), "Data has been <strong>set</strong>", 'success');
		
	}
}
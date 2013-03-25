<?php

namespace Controlpanel;

class ShipProperties extends \shipProperties{
	
	public function edit($user, $params) {
		$retVal = '';

		$data = $this->load($params['id'], true, true);

		$retVal .= BaseItem::sRenderEditForm($this, $data, $params['id']);

		return $retVal;
	}

	public function editExe($user, $params) {
		$retVal = '';

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$tQuery = BaseItem::sMakeUpdateQuery($this->tableName, $this->tableID, $this->tableUseFields, $params);

		\Database\Controller::getInstance()->execute($tQuery);

		self::sQuickRecompute($params['id']);

		$retVal .= \General\Controls::sUiDialog( "Confirmation", "Data has been <strong>set</strong>", "document.location='{$_SESSION['returnLink']}'");

		return $retVal;
	}
}
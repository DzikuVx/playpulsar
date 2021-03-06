<?php

namespace Controlpanel;

use \Database\Controller as Database;
use \Cache\Controller as Cache;

class PortTypes extends GameplayItem{

	protected $detailTitle = 'Port Type Data';
	protected $editTitle = '';
	protected $addTitle = '';

	protected $tableName = 'porttypes';
	protected $tableIdField = 'PortTypeID';

	public function editExe($user, $params) {
		$retVal = '';

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$tData = $this->getDataObject($params['id']);

		$tFields = array();
		foreach ($tData as $tKey => $tValue) {
			if ($tKey != 'PortTypeID') {
				array_push($tFields, $tKey);
			}
		}

		$tQuery = BaseItem::sMakeUpdateQuery('porttypes', 'PortTypeID', $tFields, $params);
		Database::getInstance()->execute($tQuery);
		Cache::getInstance()->clearModule('portProperties');

		$retVal .= \General\Controls::sUiDialog( "Confirmation", "Data has been <strong>set</strong>", "document.location='{$_SESSION['returnLink']}'");

		return $retVal;
	}

	public function deleteExe($user, $params) {

		Database::getInstance()->execute("DELETE FROM ports WHERE PortTypeID='{$params['id']}'");

		return parent::deleteExe($user, $params);
	}

	protected function getDataObject($itemID) {

		$tQuery = Database::getInstance()->execute ( "
				SELECT 
					porttypes.*
				FROM 
					porttypes
				WHERE 
					PortTypeID='{$itemID}' LIMIT 1" );
		return Database::getInstance()->fetch ( $tQuery );
	}

}
<?php

namespace Controlpanel;

class ScriptRun extends BaseItem{

	protected function assignDbObject() {
		$this->db = \Database\Controller::getBackendInstance();
	}

	protected function getDataObject($itemID) {

		$tQuery = $this->db->execute ( "SELECT st_scriptruns.*, (Time/Count) AS Avg FROM st_scriptruns WHERE Hash='{$itemID}' LIMIT 1" );
		return $this->db->fetch ( $tQuery );
	}

	public function clear($user, $params) {

		$tQuery = "TRUNCATE TABLE st_scriptruns";
		$this->db->execute ( $tQuery );

		$host = $_SERVER ['HTTP_HOST'];
		$uri = rtrim ( dirname ( $_SERVER ['PHP_SELF'] ), '/\\' );
		$extra = '?class=\Controlpanel\ScriptRun&method=browse';
		header ( "Location: http://$host$uri/$extra" );

	}

}
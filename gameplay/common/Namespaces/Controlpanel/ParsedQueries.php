<?php

namespace Controlpanel;

class ParsedQueries extends Queries{

	protected function getDataObject($itemID) {

		$tQuery = $this->db->execute ( "SELECT queries.*, (Time/Count) AS Avg FROM st_parsedqueries AS queries WHERE Hash='{$itemID}' LIMIT 1" );
		return $this->db->fetch ( $tQuery );
	}

	public function clear($user, $params) {

		$tQuery = "TRUNCATE TABLE st_parsedqueries";
		$this->db->execute ( $tQuery );

		$host = $_SERVER ['HTTP_HOST'];
		$uri = rtrim ( dirname ( $_SERVER ['PHP_SELF'] ), '/\\' );
		$extra = '?class=parsedQueries&method=browse';
		header ( "Location: http://$host$uri/$extra" );

	}

}
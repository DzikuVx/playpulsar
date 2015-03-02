<?php
class psScriptDebug {

	static public function sSaveExecution($action, $subaction, $tDiff) {

		$tHash = md5 ( $action.'|'.$subaction );

		try {
			$tQuery = "UPDATE st_scriptruns SET Count=Count+1, Time=Time+'{$tDiff}' WHERE Hash='{$tHash}'";
			\Database\Controller::getBackendInstance()->execute ( $tQuery );

			if (\Database\Controller::getBackendInstance()->getAffectedRows () == 0) {
				$tQuery = "INSERT DELAYED INTO st_scriptruns (Hash, Action, Subaction, Time) VALUES('{$tHash}','{$action}','{$subaction}','{$tDiff}') ";
				\Database\Controller::getBackendInstance()->execute ( $tQuery );
			}

		} catch ( Exception $e ) {
			/*
			 * Jeśli nie udało się zapisać zapytania, pomiń
			 */
		}

	}

}
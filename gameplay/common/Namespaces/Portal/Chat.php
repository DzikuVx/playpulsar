<?php

namespace Portal;

class Chat {

	public function render() {

		$sRetVal = '<ul>';

		$chatDb = \Database\Controller::getChatInstance();

		$tQuery = "SELECT * FROM chatglobal ORDER BY ChatID DESC LIMIT 12";
		$tQuery = $chatDb->executeAndRetryOnDeadlock ( $tQuery );
		while ( $tResult = $chatDb->fetch ( $tQuery ) ) {
			$tObject = unserialize ( $tResult->Data );
			$sRetVal .= '<li>'.$tObject->render ().'</li>';
		}
		$sRetVal .= '</ul>';

		return $sRetVal;
	}

}
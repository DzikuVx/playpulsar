<?php
class buddyRequestRegistry extends simpleRegistry{

	public function get() {

		$oCacheKey = new \phpCache\CacheKey('buddyRequestRegistry::get', $this->userID);
        $oCache    = \phpCache\Factory::getInstance()->create();

		if (!$oCache->check( $oCacheKey )) {

			$retVal = '';
			//todo: nawigacja po stronach
			$retVal .= "<h1>{T:Requests}</h1>";
			$retVal .= "<table class='table table-striped table-condensed linked'>";

			$retVal .= '<thead>';
			$retVal .= '<tr>';
			$retVal .= '<th>{T:name}</th>';
			$retVal .= '<th style="width: 6em;">&nbsp;</th>';
			$retVal .= '</tr>';
			$retVal .= '</thead>';
			$retVal .= '<tbody>';

			$tQuery = "SELECT
                users.*
              FROM
                buddylist JOIN users ON users.UserID=buddylist.SecondUserID
              WHERE
                buddylist.UserID = '{$this->userID}' AND
                Accepted='no'
              LIMIT 30";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			$tIndex = 0;
			while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				$tIndex ++;
				$retVal .= '<tr>';
				$retVal .= '<td>' . $tResult->Name . '</td>';

				$tString = \General\Controls::renderImgButton ( 'info', "Playpulsar.gameplay.execute('shipExamine','',null,'{$tResult->UserID}');", TranslateController::getDefault()->get ( 'examine' ) );
				$tString .= \General\Controls::renderImgButton ('add' , "Playpulsar.gameplay.execute('buddyAccept','',null,'{$tResult->UserID}');", TranslateController::getDefault()->get ( 'accept' ) );
				$tString .= \General\Controls::renderImgButton ( 'delete', "Playpulsar.gameplay.execute('buddyDecline','',null,'{$tResult->UserID}');", TranslateController::getDefault()->get ( 'decline' ) );

				$retVal .= '<td>' . $tString . '</td>';
				$retVal .= '</tr>';
			}

			$retVal .= '</table>';
			$retVal .= '</div>';
				
			if ($tIndex == 0) {
				$retVal = '';
			}
				
			$oCache->set ( $oCacheKey, $retVal, 3600 );
		} else {
			$retVal = $oCache->get ( $oCacheKey );
		}
		return $retVal;
	}
}
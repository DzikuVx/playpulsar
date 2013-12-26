<?php
class buddyRegistry extends simpleRegistry{

	public function get() {

		$oCacheKey = new \phpCache\CacheKey('buddyRegistry::get', $this->userID);
        $oCache    = \phpCache\Factory::getInstance()->create();

		if (!$oCache->check ( $oCacheKey )) {

			$retVal = '';
			$retVal .= "<h1>{T:Buddy List}</h1>";
			$retVal .= "<table class='table table-striped table-condensed linked'>";

			$retVal .= '<thead>';
			$retVal .= '<tr>';
			$retVal .= '<th>{T:name}</th>';
			$retVal .= '<th style="width: 4em;">&nbsp;</th>';
			$retVal .= '</tr>';
			$retVal .= '</thead>';
			$retVal .= '<tbody>';

			$tQuery = "SELECT
                users.*
              FROM
                buddylist JOIN users ON users.UserID=buddylist.SecondUserID
              WHERE
                buddylist.UserID = '{$this->userID}' AND
                Accepted='yes'
              LIMIT 30";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			$tIndex = 0;
			while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				$tIndex ++;
				$retVal .= '<tr>';
				$retVal .= '<td>' . $tResult->Name . '</td>';

				$tString = \General\Controls::renderImgButton ( 'info', "Playpulsar.gameplay.execute('shipExamine','',null,'{$tResult->UserID}');", TranslateController::getDefault()->get ( 'examine' ) );
				$tString .= \General\Controls::renderImgButton ( 'delete', "Playpulsar.gameplay.execute('buddyDeclineCurrent','',null,'{$tResult->UserID}');", TranslateController::getDefault()->get ( 'decline' ) );

				$retVal .= '<td>' . $tString . '</td>';
				$retVal .= '</tr>';
			}

			$retVal .= '</table>';
			$retVal .= '</div>';
				
			$oCache->set ( $oCacheKey, $retVal, 3600 );
		} else {
			$retVal = $oCache->get ( $oCacheKey );
		}
		return $retVal;
	}
}
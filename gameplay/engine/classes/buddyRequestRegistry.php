<?php
class buddyRequestRegistry extends simpleRegistry{

	public function get() {

		global $userID;

		$module = 'buddyRequestRegistry::get';
		$property = $this->userID;

		if (! \Cache\Controller::getInstance()->check ( $module, $property )) {

			$retVal = '';
			//@todo: nawigacja po stronach
			$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'Requests' ) . "</h1>";
			$retVal .= "<table class=\"transactionList linked\" cellspacing=\"2\" cellpadding=\"0\">";

			$retVal .= '<thead>';
			$retVal .= '<tr>';
			$retVal .= '<th>' . TranslateController::getDefault()->get ( 'name' ) . '</th>';
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

				$tString = \General\Controls::renderImgButton ( 'info', "executeAction('shipExamine','',null,'{$tResult->UserID}');", TranslateController::getDefault()->get ( 'examine' ) );
				$tString .= \General\Controls::renderImgButton ('add' , "executeAction('buddyAccept','',null,'{$tResult->UserID}');", TranslateController::getDefault()->get ( 'accept' ) );
				$tString .= \General\Controls::renderImgButton ( 'delete', "executeAction('buddyDecline','',null,'{$tResult->UserID}');", TranslateController::getDefault()->get ( 'decline' ) );

				$retVal .= '<td>' . $tString . '</td>';
				$retVal .= '</tr>';
			}

			$retVal .= '</table>';
			$retVal .= '</div>';
				
			if ($tIndex == 0) {
				$retVal = '';
			}
				
			\Cache\Controller::getInstance()->set ( $module, $property, $retVal, 3600 );
		} else {
			$retVal = \Cache\Controller::getInstance()->get ( $module, $property );
		}
		return $retVal;
	}
}
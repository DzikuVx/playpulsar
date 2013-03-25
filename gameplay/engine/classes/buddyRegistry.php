<?php
class buddyRegistry extends simpleRegistry{

	public function get() {

		global $userID;

		$module = 'buddyRegistry::get';
		$property = $this->userID;

		if (! \Cache\Controller::getInstance()->check ( $module, $property )) {

			$retVal = '';
			$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'Buddy List' ) . "</h1>";
			$retVal .= "<table class=\"transactionList linked\" cellspacing=\"2\" cellpadding=\"0\">";

			$retVal .= '<thead>';
			$retVal .= '<tr>';
			$retVal .= '<th>' . TranslateController::getDefault()->get ( 'name' ) . '</th>';
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

				$tString = \General\Controls::renderImgButton ( 'info', "executeAction('shipExamine','',null,'{$tResult->UserID}');", TranslateController::getDefault()->get ( 'examine' ) );
				$tString .= \General\Controls::renderImgButton ( 'delete', "executeAction('buddyDeclineCurrent','',null,'{$tResult->UserID}');", TranslateController::getDefault()->get ( 'decline' ) );

				$retVal .= '<td>' . $tString . '</td>';
				$retVal .= '</tr>';
			}

			$retVal .= '</table>';
			$retVal .= '</div>';
				
			\Cache\Controller::getInstance()->set ( $module, $property, $retVal, 3600 );
		} else {
			$retVal = \Cache\Controller::getInstance()->get ( $module, $property );
		}
		return $retVal;
	}
}
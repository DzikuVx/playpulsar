<?php
/**
 * Rejestr wiadomości
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class messageRegistry extends simpleRegistry {

	/**
	 * Pobierz wiadomości
	 *
	 * @return unknown
	 */
	public function get() {

		$retVal = '';
		//@todo: nawigacja po stronach
		$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'messages' ) . "</h1>";

		$retVal .= "<table class=\"table table-striped table-condensed\">";

		$retVal .= "<tr>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'date' ) . "</th>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'author' ) . "</th>";
		$retVal .= "<th style=\"width: 90px;\">&nbsp;</th>";
		$retVal .= "</tr>";

		$tQuery = "SELECT messages.*, users.Name FROM messages LEFT JOIN users ON users.UserID=messages.Author WHERE Receiver='{$this->userID}' ORDER BY MessageID DESC LIMIT 30";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			if ($tResult->Received == 'no') {
				$tStyle = "font-weight: bold;";
			} else {
				$tStyle = '';
			}

			$retVal .= "<tr style=\"cursor: pointer; " . $tStyle . "\">";
			$retVal .= "<td onclick=\"executeAction('showMessageText',null,null,'{$tResult->MessageID}');\">" . \General\Formater::formatDateTime ( $tResult->CreateTime ) . '</td>';
			$retVal .= "<td onclick=\"executeAction('showMessageText',null,null,'{$tResult->MessageID}');\">" . $tResult->Name . '</td>';

			$tString = '';
			$tString .= \General\Controls::renderImgButton ( 'delete', "executeAction('deleteMessage',null,null,'{$tResult->MessageID}');", 'Delete' );

			if (empty ( $tString )) {
				$tString = '&nbsp;';
			}

			$retVal .= '<td>' . $tString . '</td>';

			$retVal .= '</tr>';
		}
		$retVal .= "</table>";

		return $retVal;
	}

}
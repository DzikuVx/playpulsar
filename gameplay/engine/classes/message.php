<?php
/**
 * Wiadomość
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class message extends baseItem {

	protected $tableName = "messages";
	protected $tableID = "MessageID";
	protected $tableUseFields = null;
	protected $defaultCacheExpire = 3600;
	protected $useMemcached = true;

	/**
	 * Pobranie ilości nieprzeczytanych wiadomości
	 *
	 * @param int $userID
	 * @return int
	 */
	static public function sGetUnreadCount($userID) {

		$oCacheKey = new \Cache\CacheKey('message::sGetUnreadCount', $userID);
		
		if (!\Cache\Controller::getInstance()->check($oCacheKey)) {

			$tQuery = "SELECT COUNT(*) AS ILE FROM messages WHERE Receiver='{$userID}' AND Received='no'";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			$retVal = \Database\Controller::getInstance()->fetch ( $tQuery )->ILE;

			\Cache\Controller::getInstance()->set($oCacheKey, $retVal);

		}else {
			$retVal = \Cache\Controller::getInstance()->get($oCacheKey);
		}

		return $retVal;
	}

	/**
	 * usunięcie wiadomości
	 *
	 * @param int $messageID
	 * @return boolean
	 * @throws securityException
	 */
	static public function sDelete($messageID) {

		global $userID;

		$tMessage = self::quickLoad ( $messageID );

		if ($tMessage->Author != $userID && $tMessage->Receiver != $userID) {
			throw new securityException ( );
		}

		announcementPanel::getInstance()->write ( 'info', TranslateController::getDefault()->get ( 'messageDeleted' ) );
		$tQuery = "DELETE FROM messages WHERE MessageID='{$messageID}' LIMIT 1";
		\Database\Controller::getInstance()->execute ( $tQuery );

		\Cache\Controller::getInstance()->clear('message::sGetUnreadCount',$userID);

		messageRegistry::sRender ();

		return true;
	}

	/**
	 * Pobranie szczegółów wiadomości
	 *
	 * @param int $ID
	 * @return boolean
	 */
	function get($ID) {

		$this->dataObject = null;

		$tResult = \Database\Controller::getInstance()->execute ( "
      SELECT
        messages.*,
        sender.Name AS SenderName
      FROM
        messages LEFT JOIN users AS sender ON sender.UserID=messages.Author
      WHERE
        MessageID='$ID'
      LIMIT
        1
      " );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tResult ) ) {
			$this->dataObject = $resultRow;
		}
		$this->ID = $this->parseCacheID ( $ID );
		return true;
	}

	/**
	 * Szczegóły wiadomości
	 *
	 * @param int $messageID
	 * @return boolean
	 */
	static public function sGetDetail($messageID) {

		global $userID, $portPanel, $actionPanel;

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		$portPanel = "&nbsp;";

		$tMessage = self::quickLoad ( $messageID );

		if ($tMessage->Author != $userID && $tMessage->Receiver != $userID) {
			throw new securityException ( );
		}

		$actionPanel .= "<h1>" . TranslateController::getDefault()->get ( 'messageDetail' ) . "</h1>";
		$actionPanel .= "<table class=\"table table-striped table-condensed\">";
		/**
		 * @since 2010-07-31
		 */
		if (!empty($tMessage->Author)) {
			$actionPanel .= "<tr>";
			$actionPanel .= "<th style='width: 10em;'>" . TranslateController::getDefault()->get ( 'author' ) . "</th>";
			$actionPanel .= "<td style='cursor: pointer;' onclick=\"executeAction('shipExamine',null,null,'{$tMessage->Author}');\">" . $tMessage->SenderName . "</th>";
			$actionPanel .= "</tr>";
		}
		$actionPanel .= "<tr>";
		$actionPanel .= "<th>" . TranslateController::getDefault()->get ( 'date' ) . "</th>";
		$actionPanel .= "<td>" . \General\Formater::formatDateTime ( $tMessage->CreateTime ) . "</td>";
		$actionPanel .= "</tr>";
		$actionPanel .= "<tr>";
		$actionPanel .= "<td colspan='2'>" . $tMessage->Text . "</td>";
		$actionPanel .= "</tr>";

		$actionPanel .= "</table>";

		$actionPanel .= \General\Controls::bootstrapButton ( '{T:close}', "executeAction('showMessages',null,null,null);");
		/**
		 * @since 2010-07-31
		 */
		if (!empty($tMessage->Author)) {
			$actionPanel .= \General\Controls::bootstrapButton ( '{T:reply}', "executeAction('sendMessage',null,null,'{$tMessage->Author}');",'btn-success');
		}
		$actionPanel .= \General\Controls::bootstrapButton ( '{T:delete}', "executeAction('deleteMessage',null,null,'{$tMessage->MessageID}');",'btn-danger');

		$tQuery = "UPDATE messages SET Received='yes' WHERE MessageID='{$messageID}'";
		\Database\Controller::getInstance()->execute ( $tQuery );

		\Cache\Controller::getInstance()->clear('message::sGetUnreadCount',$userID);

		return true;
	}

	/**
	 * Wysłanie wiadomości, wykonanie
	 *
	 * @param int $author
	 * @param int $receiver
	 * @param string $text
	 */
	static public function sSendExecute($author, $receiver, $text) {

		self::sInsert($author, $receiver, $text);

		announcementPanel::getInstance()->write ( 'info', TranslateController::getDefault()->get ( 'messageSent' ) );
		messageRegistry::sRender ();
	}


	/**
	 *
	 * Zapis wiadomości do bazy danych
	 * @param int $author
	 * @param int $receiver
	 * @param string $text
	 * @return boolean
	 * @since 2010-07-31
	 */
	static public function sInsert($author, $receiver, $text) {

		$retVal = true;

		try {

			$text = \Database\Controller::getInstance()->quote ( $text );

			if ($author == null) {
				$author = "null";
			}else {
				$author = "'".$author."'";
			}

			$tQuery = "INSERT INTO messages(Author, Receiver, Text, CreateTime) VALUES({$author},'{$receiver}','{$text}','" . time () . "')";
			\Database\Controller::getInstance()->execute ( $tQuery );

			$oCacheKey = new \Cache\CacheKey('message::sGetUnreadCount', $receiver);
			
			$tVal = \Cache\Controller::getInstance()->get($oCacheKey);
			if (empty($tVal)) {
				$tVal = 0;
			}
			$tVal++;
			
			\Cache\Controller::getInstance()->set($oCacheKey, $tVal);

		}catch(Exception $e) {
			$retVal = false;
		}

		return $retVal;
	}

	/**
	 * Wysłanie wiadomości, ekran wysyłania
	 *
	 * @param int $author
	 * @param int $receiver
	 */
	static public function sSend($author, $receiver) {

		global $actionPanel, $portPanel;

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		$portPanel = "&nbsp;";

		$actionPanel .= "<h1>" . TranslateController::getDefault()->get ( 'newMessage' ) . "</h1>";

		$actionPanel .= "<table class=\"table table-striped table-condensed\">";

		$item = new userProperties ( );
		$otheruserParameters = $item->load ( $receiver, true, true );
		unset($item);

		$actionPanel .= "<tr>";
		$actionPanel .= "<th style=\"width: 20em;\">" . TranslateController::getDefault()->get ( 'receiver' ) . "</th>";
		$actionPanel .= "<td>{$otheruserParameters->Name}</td>";
		$actionPanel .= "</tr>";

		$actionPanel .= "<tr>";
		$actionPanel .= "<th>" . TranslateController::getDefault()->get ( 'text' ) . "</th>";
		$actionPanel .= "<th>" . \General\Controls::renderInput ( 'textarea', '', 'msgText', 'msgText', 1024 ) . "</th>";
		$actionPanel .= "</tr>";

		$actionPanel .= '</table>';
		$actionPanel .= "<center><div class=\"closeButton\" onClick=\"executeAction('sendMessageExecute',null,$('#msgText').val(),'{$receiver}');\">" . TranslateController::getDefault()->get ( 'send' ) . "</div></center>";
	}

	/**
	 *Konstruktor statyczny
	 *
	 * @param int $ID
	 * @return stdClass
	 */
	static public function quickLoad($ID) {
		$item = new message ( );
		$retVal = $item->load ( $ID, true, true );
		unset($item);
		return $retVal;
	}

}
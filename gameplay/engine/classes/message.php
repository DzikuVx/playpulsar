<?php

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

		$oCacheKey = new \phpCache\CacheKey('message::sGetUnreadCount', $userID);
        $oCache    = \phpCache\Factory::getInstance()->create();

		if (!$oCache->check($oCacheKey)) {

			$tQuery = "SELECT COUNT(*) AS ILE FROM messages WHERE Receiver='{$userID}' AND Received='no'";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			$retVal = \Database\Controller::getInstance()->fetch ( $tQuery )->ILE;

			$oCache->set($oCacheKey, $retVal);

		}else {
			$retVal = $oCache->get($oCacheKey);
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

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:messageDeleted}');
		$tQuery = "DELETE FROM messages WHERE MessageID='{$messageID}' LIMIT 1";
		\Database\Controller::getInstance()->execute ( $tQuery );

        \phpCache\Factory::getInstance()->create()->clear(new \phpCache\CacheKey('message::sGetUnreadCount',$userID));

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

		global $userID;

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();

		$tMessage = self::quickLoad ( $messageID );

		if ($tMessage->Author != $userID && $tMessage->Receiver != $userID) {
			throw new securityException ( );
		}

		$sRetVal = "<h1>" . TranslateController::getDefault()->get ( 'messageDetail' ) . "</h1>";
		$sRetVal .= "<table class=\"table table-striped table-condensed\">";
		/**
		 * @since 2010-07-31
		 */
		if (!empty($tMessage->Author)) {
			$sRetVal .= "<tr>";
			$sRetVal .= "<th style='width: 10em;'>" . TranslateController::getDefault()->get ( 'author' ) . "</th>";
			$sRetVal .= "<td style='cursor: pointer;' onclick=\"Playpulsar.gameplay.execute('shipExamine',null,null,'{$tMessage->Author}');\">" . $tMessage->SenderName . "</th>";
			$sRetVal .= "</tr>";
		}
		$sRetVal .= "<tr>";
		$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'date' ) . "</th>";
		$sRetVal .= "<td>" . \General\Formater::formatDateTime ( $tMessage->CreateTime ) . "</td>";
		$sRetVal .= "</tr>";
		$sRetVal .= "<tr>";
		$sRetVal .= "<td colspan='2'>" . $tMessage->Text . "</td>";
		$sRetVal .= "</tr>";

		$sRetVal .= "</table>";

		$sRetVal .= \General\Controls::bootstrapButton ( '{T:close}', "Playpulsar.gameplay.execute('showMessages',null,null,null);");
		/**
		 * @since 2010-07-31
		 */
		if (!empty($tMessage->Author)) {
			$sRetVal .= \General\Controls::bootstrapButton ( '{T:reply}', "Playpulsar.gameplay.execute('sendMessage',null,null,'{$tMessage->Author}');",'btn-success');
		}
		$sRetVal .= \General\Controls::bootstrapButton ( '{T:delete}', "Playpulsar.gameplay.execute('deleteMessage',null,null,'{$tMessage->MessageID}');",'btn-danger');

		\Gameplay\Panel\Action::getInstance()->add($sRetVal);

		$tQuery = "UPDATE messages SET Received='yes' WHERE MessageID='{$messageID}'";
		\Database\Controller::getInstance()->execute ( $tQuery );

		\phpCache\Factory::getInstance()->create()->clear(new \phpCache\CacheKey('message::sGetUnreadCount',$userID));

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

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:messageSent}');
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

			$oCacheKey = new \phpCache\CacheKey('message::sGetUnreadCount', $receiver);
            $oCache    = \phpCache\Factory::getInstance()->create();

			$tVal = $oCache->get($oCacheKey);
			if (empty($tVal)) {
				$tVal = 0;
			}
			$tVal++;

			$oCache->set($oCacheKey, $tVal);

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

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();

		$sRetVal = "<h1>{T:newMessage}</h1>";

		$sRetVal .= "<table class=\"table table-striped table-condensed\">";

		$otheruserParameters = new \Gameplay\Model\UserEntity($receiver);

		$sRetVal .= "<tr>";
		$sRetVal .= "<th style=\"width: 20em;\">{T:receiver}</th>";
		$sRetVal .= "<td>{$otheruserParameters->Name}</td>";
		$sRetVal .= "</tr>";

		$sRetVal .= "<tr>";
		$sRetVal .= "<th>{T:text}</th>";
		$sRetVal .= "<th>" . \General\Controls::renderInput ( 'textarea', '', 'msgText', 'msgText', 1024 ) . "</th>";
		$sRetVal .= "</tr>";

		$sRetVal .= '</table>';
		$sRetVal .= "<center><div class=\"closeButton\" onClick=\"Playpulsar.gameplay.execute('sendMessageExecute',null,$('#msgText').val(),'{$receiver}');\">{T:send}</div></center>";

		\Gameplay\Panel\Action::getInstance()->add($sRetVal);
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
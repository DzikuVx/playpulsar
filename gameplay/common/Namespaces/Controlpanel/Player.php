<?php

namespace Controlpanel;
use \Database\Controller as Database;
use \General\Controls as Controls;
use \Cache\Controller as Cache;

class Player extends BaseItem {

	/**
	 * Usunięcie śladów po userze
	 * @param int $playerID
	 */
	static public function sDrop($playerID) {

		Database::getInstance()->execute ( "DELETE FROM messages WHERE Author='{$playerID}' OR Receiver='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM npccontact WHERE NpcID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM npcmove WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM portcargo WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM shipcargo WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM shipequipment WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM shippositions WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM shiprouting WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM shipweapons WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM usermaps WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM userpermissions WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM userportcargo WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM userships WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM userstats WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM usertimes WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM alliancemembers WHERE UserID='{$playerID}'" );
		Database::getInstance()->execute ( "DELETE FROM users WHERE UserID='{$playerID}'" );

	}

	/**
	 * Wysłanie standardowej wiadomośći do użytkownika
	 * @param user $user
	 * @param array $params
	 * @return string
	 */
	final public function msgSend($user, $params) {

		global $config;

		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';
		$text .= '<textarea class="ui-corner-all ui-state-default" name="msgText" cols="34" rows="5"></textarea>';
		$text .= '<input type="hidden" name="class" value="\Controlpanel\Player">';
		$text .= '<input type="hidden" name="method" value="msgSendExe">';
		$text .= '<input type="hidden" name="id" value="'.$params['id'].'">';
		$text .= '</form>';

		$retVal = Controls::sUiDialog( "Send new message", $text, "player.msgSend()", "window.history.back();", 'OK','Cancel', 'height: 100px;' );

		$retVal .= '<script type="text/javascript">';
		$retVal .= '$("#dialog-message").css("height",120)';
		$retVal .= '</script>';

		return $retVal;
	}

	/**
	 * Wysłanie standardowej wiadomośći do użytkownika, wykonanie
	 * @param user $user
	 * @param array $params
	 * @return string
	 */
	final public function msgSendExe($user, $params) {

		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("INSERT INTO messages(Receiver, Text, CreateTime) VALUES('{$params['id']}', '{$params['msgText']}', '".time()."') ");
		return Controls::sUiDialog( "Confirmation", "Message has been <strong>sent</strong>", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}'");
	}

	/**
	 * Dialog o zablokowanie konta użytkownika
	 * @param user $user
	 * @param array $params
	 * @return string
	 * @since 2011-03-22
	 */
	final public function lockDialog($user, $params) {

		global $config;

		return Controls::sUiDialog( "Confirm", "Do you want to <strong>lock</strong> this account?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=lockExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );
	}

	/**
	 * Zablokowanie konta użytkownika
	 * @param user $user
	 * @param array $params
	 * @return string
	 * @since 2011-03-22
	 */
	final public function lockExe($user, $params) {

		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("UPDATE users SET UserLocked='yes' WHERE UserID='{$params['id']}'" );
		Cache::forceClear($params['id'], 'userProperties');
		return Controls::sUiDialog( "Confirmation", "Account has been <strong>locked</strong>", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}'");
	}

	/**
	 * Odblokowanie konta użytkownika
	 * @param user $user
	 * @param array $params
	 * @return string
	 * @since 2011-03-22
	 */
	final public function unlockExe($user, $params) {

		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("UPDATE users SET UserLocked='no' WHERE UserID='{$params['id']}'" );
		Cache::forceClear($params['id'], 'userProperties');
		return Controls::sUiDialog( "Confirmation", "Account has been <strong>unlocked</strong>", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}'");
	}

	/**
	 * Dialog o odblokowanie konta użytkownika
	 * @param user $user
	 * @param array $params
	 * @return string
	 * @since 2011-03-22
	 */
	final public function unlockDialog($user, $params) {

		global $config;

		return Controls::sUiDialog( "Confirm", "Do you want to <strong>unlock</strong> this account?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=unlockExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );
	}

	/**
	 * Dialog o aktywację konta użytkownika
	 * @param user $user
	 * @param array $params
	 * @return string
	 * @since 2011-03-22
	 */
	final public function activateDialog($user, $params) {

		global $config;

		return Controls::sUiDialog( "Confirm", "Do you want to <strong>activate</strong> this account?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=activateExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );
	}

	/**
	 * Aktywowanie konta użytkownika
	 * @param user $user
	 * @param array $params
	 * @return string
	 * @since 2011-03-22
	 */
	final public function activateExe($user, $params) {

		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("UPDATE users SET UserActivated='yes' WHERE UserID='{$params['id']}'" );
		Cache::forceClear($params['id'], 'userProperties');
		return Controls::sUiDialog( "Confirmation", "Account has been <strong>activated</strong>", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}'");
	}

	/**
	 * Dialog usuwanie rookie turns
	 * @param user $user
	 * @param array $params
	 * @since 2011-03-23
	 */
	final public function removeRookieDialog($user, $params) {

		global $config;

		return Controls::sUiDialog( "Confirm", "Do you want to <strong>remove all rookie turns</strong>?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=removeRookieExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );
	}

	/**
	 * Usuwanie rookie turns
	 * @param user $user
	 * @param array $params
	 * @since 2011-03-23
	 */
	final public function removeRookieExe($user, $params) {
		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("UPDATE userships SET RookieTurns='0' WHERE UserID='{$params['id']}'" );
		Cache::getInstance()->clear('shipProperties',$params['id']);
		return Controls::sUiDialog( "Confirmation", "All rookie turns <strong>have been removed</strong>", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}'");
	}

	final public function giveRookieDialog($user, $params) {

		global $config;

		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';
		$text .= '<input masked="int10" class="ui-corner-all ui-state-default" value="0" name="value" size="10" />';
		$text .= '<input type="hidden" name="class" value="\Controlpanel\Player">';
		$text .= '<input type="hidden" name="method" value="giveRookieExe">';
		$text .= '<input type="hidden" name="id" value="'.$params['id'].'">';
		$text .= '</form>';

		$retVal = Controls::sUiDialog( "Give rookie turns", $text, "player.giveRookie()", "window.history.back();", 'OK','Cancel', 'height: 100px;' );

		$retVal .= '<script type="text/javascript">';
		$retVal .= '$("#dialog-message").css("height",60)';
		$retVal .= '</script>';

		return $retVal;
	}

	final public function giveRookieExe($user, $params) {
		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("UPDATE userships SET RookieTurns=RookieTurns+'{$params['value']}' WHERE UserID='{$params['id']}'" );
		Cache::getInstance()->clear('shipProperties',$params['id']);

		return Controls::sUiDialog( "Confirmation", "Rookie turns <strong>have been set</strong>", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}'");
	}

	final public function giveTraxiumDialog($user, $params) {

		global $config;

		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';
		$text .= '<input masked="int10" class="ui-corner-all ui-state-default" value="0" name="value" size="10" />';
		$text .= '<input type="hidden" name="class" value="\Controlpanel\Player">';
		$text .= '<input type="hidden" name="method" value="giveTraxiumExe">';
		$text .= '<input type="hidden" name="id" value="'.$params['id'].'">';
		$text .= '</form>';

		$retVal = Controls::sUiDialog( "Give Traxium", $text, "player.giveRookie()", "window.history.back();", 'OK','Cancel', 'height: 100px;' );

		$retVal .= '<script type="text/javascript">';
		$retVal .= '$("#dialog-message").css("height",60)';
		$retVal .= '</script>';

		return $retVal;
	}

	final public function giveTraxiumExe($user, $params) {
		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("UPDATE userstats SET Fame=Fame+'{$params['value']}' WHERE UserID='{$params['id']}'" );
		Cache::getInstance()->clear('userStats',$params['id']);

		return Controls::sUiDialog( "Confirmation", "Traxium <strong>have been given</strong>", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}'");
	}

	final public function giveAntimatterDialog($user, $params) {

		global $config;

		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';
		$text .= '<input masked="int10" class="ui-corner-all ui-state-default" value="0" name="value" size="10" />';
		$text .= '<input type="hidden" name="class" value="\Controlpanel\Player">';
		$text .= '<input type="hidden" name="method" value="giveAntimatterExe">';
		$text .= '<input type="hidden" name="id" value="'.$params['id'].'">';
		$text .= '</form>';

		$retVal = Controls::sUiDialog( "Give antimatter", $text, "player.giveRookie()", "window.history.back();", 'OK','Cancel', 'height: 100px;' );

		$retVal .= '<script type="text/javascript">';
		$retVal .= '$("#dialog-message").css("height",60)';
		$retVal .= '</script>';

		return $retVal;
	}

	final public function giveAntimatterExe($user, $params) {
		global $config;

		Database::getInstance()->quoteAll($params);

		Database::getInstance()->execute("UPDATE userships SET Turns=Turns+'{$params['value']}' WHERE UserID='{$params['id']}'" );
		Cache::getInstance()->clear('shipProperties',$params['id']);

		return Controls::sUiDialog( "Confirmation", "Antimatter <strong>have been given</strong>", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}'");
	}

	public function detail($user, $params) {

		$_SESSION['returnLink'] = $_SERVER['REQUEST_URI'];
		$_SESSION['returnUser'] = $params['id'];

		$template = new \General\Templater('templates/playerDetail.html');

		$userPropertiesObject = new \userProperties();
		$userProperties = $userPropertiesObject->load($params['id'],true, true);

		$shipPosition = new \shipPosition($params['id']);

		$shipPropertiesObject = new \shipProperties();
		$shipProperties = $shipPropertiesObject->load($params['id'],true, true);

		$userStatsObject = new \userStats();
		$userStats = $userStatsObject->load($params['id'],true, true);

		//@todo prawa dostępu:
		$template->add('playerStatsEditButton',Controls::bootstrapButton ( "Edit", "document.location='index.php?class=\Controlpanel\UserStats&method=edit&id={$params['id']}'",'btn-warning', 'icon-pencil'));
		$template->add('shipPositionEditButton',Controls::bootstrapButton ( "Edit", "document.location='index.php?class=\Controlpanel\ShipPosition&method=edit&id={$params['id']}'",'btn-warning', 'icon-pencil'));
		$template->add('shipPropertiesEditButton',Controls::bootstrapButton ( "Edit", "document.location='index.php?class=\Controlpanel\ShipProperties&method=edit&id={$params['id']}'",'btn-warning', 'icon-pencil'));
		$template->add('userPropertiesEditButton',Controls::bootstrapButton ( "Edit", "document.location='index.php?class=\Controlpanel\UserProperties&method=edit&id={$params['id']}'",'btn-warning', 'icon-pencil'));
		$template->add('PLAYER_STATS_SUMMARY',Controls::sBuilTable($userStats));
		$template->add('PLAYER_SHIP_SUMMARY',Controls::sBuilTable($shipProperties));
		$template->add($userProperties); 
		$template->add($shipPosition);

		$tRegistry = new \playerNoticesRegistry();
		$tParams = array('playerID'=>$params['id']);
		$template->add('PLAYER_NOTICES_REGISTRY',$tRegistry->browse($user, $tParams));
		unset($tRegistry);

		$tRegistry = new PlayerWeaponsRegistry();
		$tParams = array('playerID'=>$params['id']);
		$template->add('PLAYER_WEAPONS_REGISTRY',$tRegistry->browse($user, $tParams));
		unset($tRegistry);

		$tRegistry = new PlayerEquipmentRegistry();
		$tParams = array('playerID'=>$params['id']);
		$template->add('PLAYER_EQUIPMENT_REGISTRY',$tRegistry->browse($user, $tParams));
		unset($tRegistry);

		$template->add('CLOSE_BUTTON',Controls::bootstrapButton ( "Close", "window.history.back();", 'btn-inverse','icon-off' ));

		/*
		 * Lista do tworzenia NPCów
		 */
		
		$tWeapons = new \shipWeapons($params['id']);
		$tData = $tWeapons->get('all');
		$tArray = array();
		while ($tResult = Database::getInstance()->fetch($tData)) {
			array_push($tArray, $tResult->WeaponID);
		}
		$template->add('WeaponsString', implode(',', $tArray));
		unset($tWeapons);
		unset($tData);
		unset($tArray);
		
		$tEquipment = new \shipEquipment($params['id']);
		$tData = $tEquipment->get('all');
		$tArray = array();
		while ($tResult = Database::getInstance()->fetch($tData)) {
			array_push($tArray, $tResult->EquipmentID);
		}
		$template->add('EquipmentString', implode(',', $tArray));
		unset($tEquipment);
		unset($tData);
		unset($tArray);
		
		if ($userProperties->Type!='player') {
			$template->remove('operations');
		}else {

			if ($userProperties->UserLocked == 'yes') {
				$template->add('lockButton',Controls::bootstrapButton ( "Player Unlock", "document.location='index.php?class=\Controlpanel\Player&method=unlockDialog&id={$params['id']}'",'btn-danger','icon-lock'));
			}else {
				$template->add('lockButton',Controls::bootstrapButton ( "Player Lock", "document.location='index.php?class=\Controlpanel\Player&method=lockDialog&id={$params['id']}'",'btn-danger','icon-lock'));
			}

			$template->add('msgSendButton',Controls::bootstrapButton ( "Send an in-game message", "document.location='index.php?class=\Controlpanel\Player&method=msgSend&id={$params['id']}'",'btn-danger', 'icon-envelope' ));

			if ($userProperties->UserActivated == 'no') {
				$template->add('activateButton',Controls::bootstrapButton ( "Activate Account", "document.location='index.php?class=\Controlpanel\Player&method=activateDialog&id={$params['id']}'",'btn-danger' ));
			}		else {
				$template->add('activateButton','');
			}

			/**
			 * Operacje na rookie turns
			 * @since 2011-03-23
			 */
			if ($shipProperties->RookieTurns == 0) {
				$template->add('rookieTurns',Controls::bootstrapButton ( "Give rookie turns", "document.location='index.php?class=\Controlpanel\Player&method=giveRookieDialog&id={$params['id']}'",'btn-danger','icon-plus'));
			}else {
				$template->add('rookieTurns',Controls::bootstrapButton ( "Remove rookie turns", "document.location='index.php?class=\Controlpanel\Player&method=removeRookieDialog&id={$params['id']}'",'btn-danger',' icon-remove' ));
			}

			$template->add('giveAm',Controls::bootstrapButton ( "Give antimatter", "document.location='index.php?class=\Controlpanel\Player&method=giveAntimatterDialog&id={$params['id']}'",'btn-danger','icon-plus' ));
			$template->add('giveTraxium',Controls::bootstrapButton ( "Give Traxium", "document.location='index.php?class=\Controlpanel\Player&method=giveTraxiumDialog&id={$params['id']}'",'btn-danger','icon-plus' ));

		}

		return (string)$template;
	}

}
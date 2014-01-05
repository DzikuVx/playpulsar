<?php

namespace Controlpanel;
use \Database\Controller as Database;
use Gameplay\Model\ShipEquipments;
use Gameplay\Model\ShipPosition;
use Gameplay\Model\ShipProperties;
use Gameplay\Model\ShipWeapons;
use Gameplay\Model\UserEntity;
use Gameplay\Model\UserStatistics;
use \General\Controls as Controls;

class Player extends BaseItem {

	/**
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
	 * @param \user $user
	 * @param array $params
	 * @return string
	 */
	final public function msgSend(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {
		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';
		$text .= '<textarea class="span5" name="msgText" rows="5"></textarea>';
		$text .= '<input type="hidden" name="class" value="\Controlpanel\Player">';
		$text .= '<input type="hidden" name="method" value="msgSendExe">';
		$text .= '<input type="hidden" name="id" value="'.$params['id'].'">';
		$text .= '</form>';

		$retVal = Controls::dialog( "Send new message", $text, "player.msgSend()", "window.history.back();", 'OK','Cancel');

		$retVal .= '<script type="text/javascript">';
		$retVal .= '$("#dialog-message").css("height",120)';
		$retVal .= '</script>';

		return $retVal;
	}

	/**
	 * @param \user $user
	 * @param array $params
	 * @return string
	 */
	final public function msgSendExe(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {

		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("INSERT INTO messages(Receiver, Text, CreateTime) VALUES('{$params['id']}', '{$params['msgText']}', '".time()."') ");
		
		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}", "Operation completed");
	}

	/**
	 * @param \user $user
	 * @param array $params
	 * @return string
	 */
	final public function lockDialog(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {

		global $config;

		return Controls::dialog( "Confirm", "Do you want to <strong>lock</strong> this account?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=lockExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );
	}

	/**
	 * @param \user $user
	 * @param array $params
	 * @return string
	 */
	final public function lockExe(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {

		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("UPDATE users SET UserLocked='yes' WHERE UserID='{$params['id']}'" );

        $oUser = new \Gameplay\Model\UserEntity($params['id']);
        $oUser->clearCache();

		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}", "Operation completed");
	}

	/**
	 * Odblokowanie konta użytkownika
	 * @param \user $user
	 * @param array $params
	 * @return string
	 * @since 2011-03-22
	 */
	final public function unlockExe($user, $params) {

		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("UPDATE users SET UserLocked='no' WHERE UserID='{$params['id']}'" );

        $oUser = new \Gameplay\Model\UserEntity($params['id']);
        $oUser->clearCache();

        \General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}", "Operation completed");
	}

	/**
	 * @param \user $user
	 * @param array $params
	 * @return string
	 */
	final public function unlockDialog($user, $params) {

		global $config;

		return Controls::dialog( "Confirm", "Do you want to <strong>unlock</strong> this account?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=unlockExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );
	}

	/**
	 * @param \user $user
	 * @param array $params
	 * @return string
	 */
	final public function activateDialog(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {

		global $config;

		return Controls::dialog( "Confirm", "Do you want to <strong>activate</strong> this account?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=activateExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );
	}

	/**
	 * @param \user $user
	 * @param array $params
	 * @return string
	 */
	final public function activateExe(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {

		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("UPDATE users SET UserActivated='yes' WHERE UserID='{$params['id']}'" );
        $oUser = new \Gameplay\Model\UserEntity($params['id']);
        $oUser->clearCache();
        \General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}", "Operation completed");
	}

	/**
	 * Rookie turns delete dialog
	 * @param \user $user
	 * @param array $params
     * @return string
	 */
	final public function removeRookieDialog($user, $params) {

		global $config;

		return Controls::dialog( "Confirm", "Do you want to <strong>remove all rookie turns</strong>?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=removeRookieExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );
	}

	/**
	 * Drop rookie turns for player
	 * @param \user $user
	 * @param array $params
	 * @since 2011-03-23
	 */
	final public function removeRookieExe($user, $params) {
		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("UPDATE userships SET RookieTurns='0' WHERE UserID='{$params['id']}'");
        ShipProperties::sFlushCache($params['id']);
		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}", "Operation completed");
	}

    /**
     * @param \user $user
     * @param array $params
     * @return string
     */
    final public function giveRookieDialog($user, $params) {

		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';
		$text .= '<input masked="int10" class="span1" value="0" name="value"/>';
		$text .= '<input type="hidden" name="class" value="\Controlpanel\Player">';
		$text .= '<input type="hidden" name="method" value="giveRookieExe">';
		$text .= '<input type="hidden" name="id" value="'.$params['id'].'">';
		$text .= '</form>';

		$retVal = Controls::dialog( "Give rookie turns", $text, "player.giveRookie()", "window.history.back();", 'OK','Cancel', 'height: 100px;' );

		$retVal .= '<script type="text/javascript">';
		$retVal .= '$("#dialog-message").css("height",60)';
		$retVal .= '</script>';

		return $retVal;
	}

	final public function giveRookieExe($user, $params) {
		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("UPDATE userships SET RookieTurns=RookieTurns+'{$params['value']}' WHERE UserID='{$params['id']}'" );
        ShipProperties::sFlushCache($params['id']);
		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}", "Operation completed");
	}

	final public function giveTraxiumDialog(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {
		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';
		$text .= '<input masked="int10" class="span1" value="0" name="value"/>';
		$text .= '<input type="hidden" name="class" value="\Controlpanel\Player">';
		$text .= '<input type="hidden" name="method" value="giveTraxiumExe">';
		$text .= '<input type="hidden" name="id" value="' . $params['id'] . '">';
		$text .= '</form>';

		$retVal = Controls::dialog( "Give Traxium", $text, "player.giveRookie()", "window.history.back();", 'OK','Cancel', 'height: 100px;' );

		$retVal .= '<script type="text/javascript">';
		$retVal .= '$("#dialog-message").css("height",60)';
		$retVal .= '</script>';

		return $retVal;
	}

	final public function giveTraxiumExe($user, $params) {
		global $config;

		Database::getInstance()->quoteAll($params);
		Database::getInstance()->execute("UPDATE userstats SET Fame=Fame+'{$params['value']}' WHERE UserID='{$params['id']}'" );
        UserStatistics::sFlushCache($params['id']);
		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}", "Operation completed");
	}

	final public function giveAntimatterDialog($user, $params) {

		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';
		$text .= '<input masked="int10" class="span1" value="0" name="value" />';
		$text .= '<input type="hidden" name="class" value="\Controlpanel\Player">';
		$text .= '<input type="hidden" name="method" value="giveAntimatterExe">';
		$text .= '<input type="hidden" name="id" value="'.$params['id'].'">';
		$text .= '</form>';

		$retVal = Controls::dialog( "Give antimatter", $text, "player.giveRookie()", "window.history.back();", 'OK','Cancel', 'height: 100px;' );

		$retVal .= '<script type="text/javascript">';
		$retVal .= '$("#dialog-message").css("height",60)';
		$retVal .= '</script>';

		return $retVal;
	}

	final public function giveAntimatterExe($user, $params) {
		global $config;

		Database::getInstance()->quoteAll($params);

		Database::getInstance()->execute("UPDATE userships SET Turns=Turns+'{$params['value']}' WHERE UserID='{$params['id']}'" );
        ShipProperties::sFlushCache($params['id']);
		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}", "Operation completed");
	}

	public function detail($user, $params) {

		$_SESSION['returnLink'] = $_SERVER['REQUEST_URI'];
		$_SESSION['returnUser'] = $params['id'];

		$template = new \General\Templater('templates/playerDetail.html');

        $userProperties = new UserEntity($params['id']);
        $shipPosition = new ShipPosition();
		$shipProperties = new ShipProperties($params['id']);
        $userStats = new UserStatistics($params['id']);

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
		$tWeapons = new ShipWeapons($params['id']);
		$tData = $tWeapons->get('all');
		$tArray = array();
		while ($tResult = Database::getInstance()->fetch($tData)) {
			array_push($tArray, $tResult->WeaponID);
		}
		$template->add('WeaponsString', implode(',', $tArray));
		unset($tWeapons);
		unset($tData);
		unset($tArray);
		
		$tEquipment = new ShipEquipments($params['id']);
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

			$template->add('msgSendButton',Controls::bootstrapButton ( "Send an in-game message", "document.location='index.php?class=\Controlpanel\Player&method=msgSend&id={$params['id']}'",'', 'icon-envelope' ));

			if ($userProperties->UserActivated == 'no') {
				$template->add('activateButton',Controls::bootstrapButton ( "Activate Account", "document.location='index.php?class=\Controlpanel\Player&method=activateDialog&id={$params['id']}'",'btn-danger' ));
			}		else {
				$template->add('activateButton','');
			}

			/**
			 * Operacje na rookie turns
			 */
			if ($shipProperties->RookieTurns == 0) {
				$template->add('rookieTurns',Controls::bootstrapButton ( "Give rookie turns", "document.location='index.php?class=\Controlpanel\Player&method=giveRookieDialog&id={$params['id']}'",'','icon-plus'));
			}else {
				$template->add('rookieTurns',Controls::bootstrapButton ( "Remove rookie turns", "document.location='index.php?class=\Controlpanel\Player&method=removeRookieDialog&id={$params['id']}'",'',' icon-remove' ));
			}

			$template->add('giveAm',Controls::bootstrapButton ( "Give antimatter", "document.location='index.php?class=\Controlpanel\Player&method=giveAntimatterDialog&id={$params['id']}'",'','icon-plus' ));
			$template->add('giveTraxium',Controls::bootstrapButton ( "Give Traxium", "document.location='index.php?class=\Controlpanel\Player&method=giveTraxiumDialog&id={$params['id']}'",'','icon-plus' ));

		}

		return (string)$template;
	}

}
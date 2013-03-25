<?php

namespace Controlpanel;

class PlayerWeapon extends \weapon{

	private function sRenderSelect($currentID = null, $name='value') {

		$retVal = "<select name='{$name}' size='1' class='ui-state-default ui-corner-all'>";

		$tQuery = "SELECT * FROM weapontypes WHERE PortWeapon='no' ORDER BY NameEN";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);

		while ($tResult = \Database\Controller::getInstance()->fetch($tQuery)) {

			if ($currentID == $tResult->WeaponID) {
				$tString = 'selected';
			}else {
				$tString = '';
			}

			$retVal .= "<option {$tString} value='{$tResult->WeaponID}'>{$tResult->NameEN}</option>";
		}

		$retVal .= "</select>";


		return $retVal;
	}

	final public function delete($user, $params) {
		global $config;

		return \General\Controls::sUiDialog( "Confirm", "Do you want to <strong>delete</strong> this weapon?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=deleteExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );

	}

	final public function deleteExe($user, $params) {

		global $config;

		\Database\Controller::getInstance()->quoteAll($params);
		\Database\Controller::getInstance()->execute("DELETE FROM shipweapons WHERE ShipWeaponID = '{$params['id']}'");
		if (!empty($_SESSION['returnUser'])) {
			\Cache\Controller::forceClear($_SESSION['returnUser'], 'shipWeapons');
			\Cache\Controller::forceClear($_SESSION['returnUser'], 'shipProperties');

			\shipProperties::sQuickRecompute($_SESSION['returnUser']);
		}
		return \General\Controls::sUiDialog( "Confirmation", "Weapon has been <strong>deleted</strong>", "document.location='{$_SESSION['returnLink']}'");
	}

	final public function add($user, $params) {

		global $config;

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';
		$text .= self::sRenderSelect();
		$text .= '<input type="hidden" name="class" value="\Controlpanel\PlayerWeapon">';
		$text .= '<input type="hidden" name="method" value="addExe">';
		$text .= '<input type="hidden" name="id" value="'.$_SESSION['returnUser'].'">';
		$text .= '</form>';

		$retVal = \General\Controls::sUiDialog( "Give player new weapon", $text, "player.addWeapon()", "window.history.back();", 'OK','Cancel', 'height: 100px;' );

		$retVal .= '<script type="text/javascript">';
		$retVal .= '$("#dialog-message").css("height",120)';
		$retVal .= '</script>';

		return $retVal;
	}

	final public function addExe($user, $params) {

		global $config;

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		\Cache\Controller::forceClear($_SESSION['returnUser'], 'shipWeapons');
		\Cache\Controller::forceClear($_SESSION['returnUser'], 'shipProperties');

		$shipWeapons = new \shipWeapons($_SESSION['returnUser']);

		$weapon = \weapon::quickLoad($params['value']);

		$shipPropertiesObject = new \shipProperties();
		$shipProperties = $shipPropertiesObject->load($_SESSION['returnUser'],true, true);

		$shipWeapons->insert($weapon, $shipProperties);

		$shipPropertiesObject->synchronize($shipProperties, true, true);

		return \General\Controls::sUiDialog( "Confirmation", "Weapon has been <strong>added</strong>", "document.location='{$_SESSION['returnLink']}'");

	}

}
<?php

namespace Controlpanel;

use Gameplay\Model\ShipProperties;
use Gameplay\Model\ShipWeapons;
use Gameplay\Model\WeaponType;

class PlayerWeapon extends WeaponType{

	private function sRenderSelect($currentID = null, $name='value') {

		$retVal = "<select name='{$name}' size='1' class='span4'>";

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

		return \General\Controls::dialog( "Confirm", "Do you want to <strong>delete</strong> this weapon?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=deleteExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );

	}

	final public function deleteExe($user, $params) {

		\Database\Controller::getInstance()->quoteAll($params);
		\Database\Controller::getInstance()->execute("DELETE FROM shipweapons WHERE ShipWeaponID = '{$params['id']}'");
		if (!empty($_SESSION['returnUser'])) {
            ShipProperties::sFlushCache($_SESSION['returnUser']);
            ShipProperties::sQuickRecompute($_SESSION['returnUser']);
		}
		
		\General\Controls::reloadWithMessage(\General\Session::get('returnLink'), "Weapon has been <strong>deleted</strong>", 'success');
	}

	final public function add($user, $params) {

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';
		$text .= self::sRenderSelect();
		$text .= '<input type="hidden" name="class" value="\Controlpanel\PlayerWeapon">';
		$text .= '<input type="hidden" name="method" value="addExe">';
		$text .= '<input type="hidden" name="id" value="'.$_SESSION['returnUser'].'">';
		$text .= '</form>';

		$retVal = \General\Controls::dialog( "Give player new weapon", $text, "player.addWeapon()", "window.history.back();", 'OK','Cancel', 'height: 100px;' );

		$retVal .= '<script type="text/javascript">';
		$retVal .= '$("#dialog-message").css("height",120)';
		$retVal .= '</script>';

		return $retVal;
	}

	final public function addExe($user, $params) {

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

        ShipProperties::sFlushCache($_SESSION['returnUser']);

		$shipWeapons = new ShipWeapons($_SESSION['returnUser']);

        $weapon = new WeaponType($params['value']);
		$shipProperties = new ShipProperties($_SESSION['returnUser']);

		$shipWeapons->insert($weapon, $shipProperties);

		$shipProperties->synchronize();

		\General\Controls::reloadWithMessage(\General\Session::get('returnLink'), "Data has been <strong>set</strong>", 'success');
		
	}

}
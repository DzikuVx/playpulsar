<?php

namespace Controlpanel;

use Gameplay\Model\EquipmentType;
use Gameplay\Model\ShipEquipments;
use Gameplay\Model\ShipProperties;

class PlayerEquipment extends EquipmentType{

	/**
	 * @param int $currentID
	 * @param string $name
	 * @return string
	 */
	private function sRenderSelect($currentID = null, $name='value') {

		$retVal = "<select name='{$name}' size='1' class='span4'>";

		$tQuery = "SELECT * FROM equipmenttypes ORDER BY NameEN";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);

		while ($tResult = \Database\Controller::getInstance()->fetch($tQuery)) {

			if ($currentID == $tResult->EquipmentID) {
				$tString = 'selected';
			}else {
				$tString = '';
			}

			$retVal .= "<option {$tString} value='{$tResult->EquipmentID}'>{$tResult->NameEN}</option>";
		}

		$retVal .= "</select>";


		return $retVal;
	}

	final public function add($user, $params) {

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';
		$text .= self::sRenderSelect();
		$text .= '<input type="hidden" name="class" value="\Controlpanel\PlayerEquipment">';
		$text .= '<input type="hidden" name="method" value="addExe">';
		$text .= '<input type="hidden" name="id" value="'.$_SESSION['returnUser'].'">';
		$text .= '</form>';

		$retVal = \General\Controls::dialog( "Give player new equipment", $text, "player.addEquipment()", "window.history.back();", 'OK','Cancel', 'height: 100px;' );

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

		$shipEquipment = new ShipEquipments($_SESSION['returnUser']);

		$equipment = new EquipmentType($params['value']);

		$shipProperties = new ShipProperties($_SESSION['returnUser']);

		$shipEquipment->insert($equipment, $shipProperties);

		$shipProperties->synchronize();

		\General\Controls::reloadWithMessage(\General\Session::get('returnLink'), "Equipment has been <strong>added</strong>", 'success');

	}

	final public function delete($user, $params) {
		global $config;

		return \General\Controls::dialog( "Confirm", "Do you want to <strong>delete</strong> this equipment?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=deleteExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );

	}

	final public function deleteExe($user, $params) {

        $oDb = \Database\Controller::getInstance();

        $oDb->quoteAll($params);
        $oDb->execute("DELETE FROM shipequipment WHERE ShipEquipmentID = '{$params['id']}'");

        if (!empty($_SESSION['returnUser'])) {
            ShipProperties::sFlushCache($_SESSION['returnUser']);
            ShipProperties::sQuickRecompute($_SESSION['returnUser']);
		}
		
		\General\Controls::reloadWithMessage(\General\Session::get('returnLink'), "Equipment has been <strong>deleted</strong>", 'success');
	}

}
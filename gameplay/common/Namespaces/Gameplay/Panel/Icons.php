<?php

namespace Gameplay\Panel;

use Gameplay\Model\ShipWeapons;
use Interfaces\Singleton;

class Icons extends Renderable implements Singleton {

	protected $panelTag = "Icons";
	protected $onEmpty = "hide";

	public function render() {

		$this->rendered = true;

		$this->retVal = '';

		if (\message::sGetUnreadCount ( $this->userID ) > 0) {
			$this->retVal .= \General\Controls::renderImgButton('message', "Playpulsar.gameplay.execute('showMessages');", '{T:messages}');
		}

		if (\shipEquipment::sGetDamagedCount( $this->userID ) > 0) {
			$this->retVal .= \General\Controls::renderImgButton('warningA', "Playpulsar.gameplay.execute('equiapmentManagement');", '{T:Damaged equipment}');
		}

		if (ShipWeapons::sGetDamagedCount( $this->userID ) > 0) {
			$this->retVal .= \General\Controls::renderImgButton('warningB', "Playpulsar.gameplay.execute('weaponsManagement');", '{T:Damaged weapons}');
		}

	}

	/**
	 *
	 * @var Icons
	 */
	private static $instance = null;

	/**
	 * @return \Gameplay\Panel\Icons
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			$className = __CLASS__;

            $userProperties = \Gameplay\PlayerModelProvider::getInstance()->get('UserEntity');

			self::$instance = new $className($userProperties->Language);
		}
		return self::$instance;
	}

}


<?php
/**
 * Panel linków
 *
 * @version $Rev: 284 $
 * @package Engine
 */
class iconPanel extends basePanel {

	protected $panelTag = "iconPanel";
	protected $onEmpty = "hide";

	public function render() {

		$this->rendered = true;

		$this->retVal = '';

		if ( message::sGetUnreadCount ( $this->userID ) > 0) {
			$this->retVal .= \General\Controls::renderImgButton('message', "executeAction('showMessages',null, null, null);", TranslateController::getDefault()->get('messages'));
		}

		if ( shipEquipment::sGetDamagedCount( $this->userID ) > 0) {
			$this->retVal .= \General\Controls::renderImgButton('warningA', "executeAction('equiapmentManagement',null, null, null);", TranslateController::getDefault()->get('Damaged equipment'));
		}

		if ( shipWeapons::sGetDamagedCount( $this->userID ) > 0) {
			$this->retVal .= \General\Controls::renderImgButton('warningB', "executeAction('weaponsManagement',null, null, null);", TranslateController::getDefault()->get('Damaged weapons'));
		}

	}

	private static $instance = null;

	/**
	 * Konstruktor statyczny
	 * @return iconPanel
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			$className = __CLASS__;

			global $userProperties;

			self::$instance = new $className($userProperties->Language);
		}
		return self::$instance;
	}

}


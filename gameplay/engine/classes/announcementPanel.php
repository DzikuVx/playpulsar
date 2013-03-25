<?php
/**
 * Panel wiadomości
 * @author Paweł
 * @since 2011-03-21
 * @see basePanel
 *
 */
class announcementPanel extends basePanel {
	protected $panelTag = "announcementPanel";

	/**
	 * Jak ma się zachować panel gdy jego zawartość jest pusta: none / hide / clear
	 * @var string
	 */
	protected $onEmpty = "hide";

	public function write($type, $text) {

		switch ($type) {
			case 'info' :
				$this->retVal = "<div class=\"infoAnnouncement\">" . $text . "</div>";
				break;

			case 'warning' :
				$this->retVal = "<div class=\"warningAnnouncement\">" . $text . "</div>";
				break;

			case 'error' :
				$this->retVal = "<div class=\"errorAnnouncement\">" . $text . "</div>";
				break;
		}
	}

	final public function populate($text) {
		$this->retVal .= $text;
	}

	private static $instance = null;

	/**
	 * Konstruktor statyczny
	 * @return announcementPanel
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

}
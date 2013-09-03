<?php
/**
 * Panel linków
 *
 * @version $Rev: 460 $
 * @package Engine
 */ 
class linksPanel extends basePanel {

	protected $panelTag = "linksPanel";

	public function render() {
		global $userAlliance, $config;

		$this->rendered = true;

		$this->retVal .= "<div style='cursor: pointer;' onclick=\"Playpulsar.gameplay.execute('examineMe',null,null,null,null);\"><img src='{$config['general']['cdn']}gfx/right2.png' />" . TranslateController::getDefault()->get ( 'My profile' ) . '</div>';
		$this->retVal .= "<div style='cursor: pointer;' onclick=\"Playpulsar.gameplay.execute('showOnlinePlayers',null, null, null);\"><img src='{$config['general']['cdn']}gfx/right2.png' />" . TranslateController::getDefault()->get ( 'onlinePlayers' )  . ' [' . user::sGetOnlineCount () . ']</div>';
		$this->retVal .= "<div style='cursor: pointer;' onclick=\"Playpulsar.gameplay.execute('showMessages',null, null, null);\"><img src='{$config['general']['cdn']}gfx/right2.png' />" . TranslateController::getDefault()->get ( 'messages' ) . ' [' . message::sGetUnreadCount ( $this->userID ) . ']</div>';
		$this->retVal .= "<div style='cursor: pointer;' onclick=\"Playpulsar.gameplay.execute('topPlayersShow','Experience', null, null);\"><img src='{$config['general']['cdn']}gfx/right2.png' />" . TranslateController::getDefault()->get ( 'topPlayers' ) . '</div>';
		if (!empty($userAlliance->AllianceID)) {
			$this->retVal .= "<div style='cursor: pointer;' onclick=\"Playpulsar.gameplay.execute('allianceDetail',null, null, '{$userAlliance->AllianceID}');\"><img src='{$config['general']['cdn']}gfx/right2.png' />" . TranslateController::getDefault()->get ( 'myAlliance' ) . '</div>';
		}
		$this->retVal .= "<div style='cursor: pointer;' onclick=\"Playpulsar.gameplay.execute('showAlliances',null, null, null);\"><img src='{$config['general']['cdn']}gfx/right2.png' />" . TranslateController::getDefault()->get ( 'alliances' ) . '</div>';
		$this->retVal .= "<div style='cursor: pointer;' onclick=\"Playpulsar.gameplay.execute('showBuddy',null, null, null);\"><img src='{$config['general']['cdn']}gfx/right2.png' />" . TranslateController::getDefault()->get ( 'Buddy List' ) . '</div>';
		$this->retVal .= "<div><a href='".TranslateController::getDefault()->get('helpFiles')."' target='_blank'><img src='{$config['general']['cdn']}gfx/right2.png' />" . TranslateController::getDefault()->get ( 'Help' ) . '</a></div>';
		$this->retVal .= "<div><a href='http://board.playpulsar.com' target='_blank'><img src='{$config['general']['cdn']}gfx/right2.png' />Forum</a></div>";
		$this->retVal .= "<div style='cursor: pointer;' onclick=\"document.location='logout.php'\"><img src='{$config['general']['cdn']}gfx/right2.png' />" . TranslateController::getDefault()->get ( 'Logout' ) . '</div>';

	}

	private static $instance = null;
	
	/**
	 * Konstruktor statyczny
	 * @return linksPanel
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
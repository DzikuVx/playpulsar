<?php
/**
 * Panel statków w sektorze
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class sectorShipsPanel extends basePanel {
	protected $onEmpty = "hideIfRendered";
	protected $panelTag = "sectorShipsPanel";

	private static $instance = null;
	
	/**
	 * Konstruktor statyczny
	 * @return sectorShipsPanel
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			$className = __CLASS__;
	
			global $userProperties;
	
			self::$instance = new $className($userProperties->Language);
		}
		return self::$instance;
	}
	
	public function render($userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties) {

		global $config, $userStats, $userAlliance;

		$this->rendered = true;

		$this->retVal = '';
		$nameField = "Name" . $this->language;

		$tQuery = "SELECT
        userships.UserID AS UserID,
        userships.RookieTurns AS RookieTurns,
        users.Name AS PlayerName,
        users.Type AS UserType,
        userstats.Level AS Level,
        specializations.$nameField AS SpecializationName,
        shiptypes.$nameField AS ShipTypeName,
        userships.OffRating AS OffRating,
        userships.DefRating AS DefRating,
        userships.Cloak AS Cloak,
        alliances.Name As AllianceName,
        alliances.AllianceID As AllianceID,
        npctypes.Behavior,
        usertimes.LastAction
      FROM
        shippositions JOIN userships ON userships.UserID=shippositions.UserID
        JOIN shiptypes ON shiptypes.ShipID = userships.ShipID
        JOIN users ON users.UserID = shippositions.UserID
        JOIN userstats ON userstats.UserID=shippositions.UserID
        LEFT JOIN specializations ON specializations.SpecializationID = userships.SpecializationID
        LEFT JOIN alliancemembers ON alliancemembers.UserID=shippositions.UserID
        LEFT JOIN alliances ON alliances.AllianceID = alliancemembers.AllianceID
        LEFT JOIN npctypes ON npctypes.NPCTypeID=users.NPCTypeID
        LEFT JOIN usertimes ON usertimes.UserID=users.UserID
      WHERE
        shippositions.System={$shipPosition->System} AND
        shippositions.X={$shipPosition->X} AND
        shippositions.Y={$shipPosition->Y} AND
        shippositions.Docked='{$shipPosition->Docked}' AND
        shippositions.UserID != '$userID' AND 
        userstats.Experience > 0
      ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		if (\Database\Controller::getInstance()->count ( $tQuery ) > 0) {
			$this->retVal .= "<center>";
			$this->retVal .= "<h1>" . TranslateController::getDefault()->get ( 'ships' ) . "</h1>";
		}

		$shipDisplayed = false;

		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			/*
			 * To jest dodatkowy warunek: gracze z tego samego sojuszu zawsze się widzą
			*/
			if (empty($userAlliance->AllianceID) || $userAlliance->AllianceID != $tR1->AllianceName) {
					
				/**
				 * sprawdz widzialność
				 */
				if ($shipPosition->Docked == 'no' && ! shipProperties::sGetVisibility ( $shipProperties, $userStats, $tR1, $tR1, $sectorProperties )) {
					continue;
				}
			}

			$shipDisplayed = true;

			if ($tR1->AllianceName == null) {
				$tR1->AllianceName = TranslateController::getDefault()->get ( 'noalliance' );
			}

			$this->retVal .= "<div class='shipPanel'>";
			$this->retVal .= "<table class='shipPanel'><tr>";
			$this->retVal .= "<td style='width: 110px;'>{$tR1->AllianceName}</td>";
			$this->retVal .= "<td style='text-align: left;'><div>";
			$this->retVal .= "<span style='font-size: 9pt; color: #f0f000; margin-right: 16px;'>{$tR1->PlayerName}</span>";
			$this->retVal .= "<span style='color: #00f000;'>" . TranslateController::getDefault()->get ( 'level' ) . ": {$tR1->Level}</span>";
			$this->retVal .= "</div><div>";

			$tDisplay = false;
			if ($tR1->UserType == 'npc') {
				$tDisplay = true;
			}else {

				if (empty($tR1->LastAction)) {
					$tR1->LastAction = 0;
				}

				if (time() - $tR1->LastAction < $config ['user'] ['onlineThreshold']) {
					$tDisplay = true;
				}
			}
			/*
			 * Pobierz jego userFastTimes
			*/

			if ($tDisplay) {
				$this->retVal .= "<img style='margin-right: 4px; margin-top: 2px;' src='{$config['general']['cdn']}gfx/pplonline.png' title='".TranslateController::getDefault()->get('Online')."' />";
			}

			if ($tR1->RookieTurns > 0) {
				$this->retVal .= "<img style='margin-right: 4px; margin-top: 2px;' src='{$config['general']['cdn']}gfx/hasrookie.png' title='".TranslateController::getDefault()->get('Rookie protected')."' />";
			}
			$this->retVal .= "<span style='margin-right: 8px;'>{$tR1->SpecializationName}</span>";
			$this->retVal .= "<span style='margin-right: 12px;'>{$tR1->ShipTypeName}</span>";
			$this->retVal .= "<span style='color: #f0f000;'>{$tR1->OffRating}/{$tR1->DefRating}</span>";
			$this->retVal .= "</div>";
			$this->retVal .= "</td>";
			$this->retVal .= "<td style='width: 35px;'>";
			$this->retVal .= \General\Controls::renderImgButton ( 'examine',"executeAction('shipExamine','',null,{$tR1->UserID});",TranslateController::getDefault()->get ( 'examine' ), 'imgMove' );
			$this->retVal .= "</td>";
			$this->retVal .= "<td style='width: 35px;'>";
			if ($shipPosition->Docked == "no" && $tR1->RookieTurns < 1 && $shipProperties->RookieTurns < 1 && ($tR1->AllianceID != $userAlliance->AllianceID || empty($userAlliance->AllianceID))) {
			$this->retVal .= \General\Controls::renderImgButton ( 'attack',"executeAction('shipAttack',null,null,{$tR1->UserID},null);",TranslateController::getDefault()->get ( 'attack' ), 'imgMove' );
					
				/*
				 * A tutaj jest triggerowane zachowanie typu AGGRESIVE
				*/

				if ($tR1->Behavior == 'aggresive') {

					npc::sAggresiveController($userID, $tR1->UserID, $userStats->Level, $tR1->Level, $sectorProperties->Visibility);

				}
					
			}
			$this->retVal .= "</td>";
			$this->retVal .= "</tr></table>";
			$this->retVal .= "</div>";

			//Jeśli jest to NPC, spróbuj wpisać go do tabeli NPC Contact
			if ($tR1->UserType == 'npc' && additional::checkRand ( $config ['npc'] ['contactProbablity'], $config ['npc'] ['contactProbablityMax'] )) {
				npc::sInsertContact ( $userID, $tR1->UserID, $shipPosition );
			}
		}

		/*
		 * Jeśli żaden statek nie był wyświetlony, ukryj panel
		*/
		if (! $shipDisplayed) {
			$this->retVal = '';
		}

		return true;
	}

}

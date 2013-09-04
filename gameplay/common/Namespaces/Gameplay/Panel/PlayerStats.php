<?php

namespace Gameplay\Panel;

use \TranslateController as Translate;

class PlayerStats extends Base {

	static private $instance = null;

	/**
	 * @throws \Exception
	 * @return \Gameplay\Panel\ShortStats
	 */
	static public function getInstance() {

		if (empty(self::$instance)) {
			throw new \Exception('Panel not initialized');
		}
		else {
			return self::$instance;
		}

	}

	static public function initiateInstance($language = 'pl', $localUserID = null) {
		self::$instance = new self($language, $localUserID);
	}

	protected $panelTag = "shortUserStatsPanel";

	/**
	 *
	 * Panel render
	 * @param \stdClass $shipProperties
	 */
	public function render($userStats, $shipProperties) {
		
		$this->rendered = true;
		
		$this->retVal = '';

		$this->retVal .= "<div class='row'>";
		$this->retVal .= "<div class='column50 em11'>";

		$this->retVal .= "<div class='columnData'>";
		$this->retVal .= "<div>";
		$this->retVal .= "<strong>".Translate::getDefault()->get ( 'level' ) . ":</strong> " . $userStats->Level;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= "<strong>".Translate::getDefault()->get ( 'experience' ) . ":</strong> " . number_format ( $userStats->Experience, 0, "", " " );
		$this->retVal .= "</div>";

		$this->retVal .= "<div>";
		$this->retVal .= "<strong>".Translate::getDefault()->get ( 'cash' ) . ":</strong> " . number_format ( $userStats->Cash, 0, "", " " ) . "$";
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= "<strong>".Translate::getDefault()->get ( 'Fame' ) . ":</strong> " . number_format ( $userStats->Fame, 0, "", " " );
		$this->retVal .= "</div>";
		
		$this->retVal .= "</div>";
		$this->retVal .= "</div>";

		$this->retVal .= "<div class='column25 center'>";

		$iExpForCurrent = \userStats::computeExperience($userStats->Level);
		$iExpForNext = \userStats::computeExperience($userStats->Level + 1);
		
		$iDiff = $iExpForNext-$iExpForCurrent;
		$iCurrent = $userStats->Experience - $iExpForCurrent;
		$iValue = ceil(($iCurrent / $iDiff) * 100);
		$this->retVal .= "<label style='color: #DBCD64;' title='".Translate::getDefault()->get ( 'Progress for next level [%]' )."'>".Translate::getDefault()->get ( 'Next lvl.' )."</label>";
		$this->retVal .= "<input data-skin='tron' class='knob' data-width='50' data-fgColor='#DBCD64' data-thickness='.2' data-readOnly=true data-max='100' value='{$iValue}'>";
		$this->retVal .= "</div>";
		
		$this->retVal .= "<div class='column25 center'>";
		$this->retVal .= "<label style='color: #BBFF47;' title='".Translate::getDefault()->get ( 'Used cargo space' )."'>".Translate::getDefault()->get ( 'cargo' )."</label>";
		$this->retVal .= "<input data-skin='tron' class='knob' data-width='50' data-fgColor='#BBFF47' data-thickness='.2' data-readOnly=true data-max='{$shipProperties->CargoMax}' value='{$shipProperties->Cargo}'>";
		$this->retVal .= "</div>";
		
		$this->retVal .= "</div>";

		date_default_timezone_set('UTC');
		$this->retVal .= "<hr /><div>";
		$this->retVal .= "<strong>".Translate::getDefault()->get ( 'Galaxy date' ) . ":</strong> " . date('Y.m.d');
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= "<strong>".Translate::getDefault()->get ( 'Galaxy time' ) . ":</strong> " . date('H:i:s');
		$this->retVal .= "</div>";

		date_default_timezone_set ( "Europe/Warsaw" );
	}

}
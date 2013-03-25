<?php
/**
 * Panel skróconych statystyk gracza
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class shortUserStatsPanel extends basePanel {
	protected $panelTag = "shortUserStatsPanel";

	/**
	 * Wyrenderowanie panelu
	 *
	 * @param stdClass $userStats
	 * @param stdClass $shipProperties
	 */
	public function render($userStats, $shipProperties) {
		$this->retVal = "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'level' ) . ": " . $userStats->Level;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'experience' ) . ": " . number_format ( $userStats->Experience, 0, "", " " );
		$this->retVal .= "</div>";

		$this->retVal .= \General\Controls::sHorizontalBar($userStats->Experience - userStats::computeExperience($userStats->Level), userStats::computeExperience($userStats->Level+1)-userStats::computeExperience($userStats->Level), array('width'=>193,'height'=>8, 'title'=> TranslateController::getDefault()->get ( 'experienceForNextLevel' )));

		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'cash' ) . ": " . number_format ( $userStats->Cash, 0, "", " " ) . "$";
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'Fame' ) . ": " . number_format ( $userStats->Fame, 0, "", " " );
		$this->retVal .= "</div>";

		date_default_timezone_set('UTC');
		$this->retVal .= "<hr /><div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'Galaxy date' ) . ": " . date('Y.m.d');
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'Galaxy time' ) . ": " . date('H:i:s');
		$this->retVal .= "</div>";

		date_default_timezone_set ( "Europe/Warsaw" );

		if ($shipProperties->RookieTurns > 0) {
			$this->retVal .= "<div>";
			$this->retVal .= TranslateController::getDefault()->get ( 'rookie' ) . ": " . number_format ( $shipProperties->RookieTurns, 0, "", "" );
			$this->retVal .= "</div>";
		}
	}
}

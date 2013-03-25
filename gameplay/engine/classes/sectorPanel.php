<?php
/**
 * Panel informacji o sektorze
 * 
 *@version $Rev: 460 $
 * @package Engine
 */
class sectorPanel extends basePanel {
	protected $panelTag = "sectorPanel";
	
	/**
	 * Render
	 *
	 * @param stdClass $sectorProperties
	 * @param stdClass $systemProperties
	 * @param stdClass $shipPosition
	 * @return boolean
	 */
	public function render($sectorProperties, $systemProperties, $shipPosition = null) {
		
		global $config;

		$this->rendered = true;
		$this->retVal = "";
		
		if ($this->renderCloser)
			$this->retVal .= $this->renderCloser ();
		
		$this->retVal .= "<table border=\"0\">";
		$this->retVal .= "<tr>";
		$this->retVal .= "<td class=\"sectorImageCell\">";
		$this->retVal .= "<img src='{$config['general']['cdn']}{$sectorProperties->Image}' class=\"sectorImage\" />";
		$this->retVal .= "</td>";
		$this->retVal .= "<td class=\"sectorInfoCell\">";
		$this->retVal .= "<div class=\"galaxyName\">" . TranslateController::getDefault()->get ( 'galaxy' ) . ": " . $systemProperties->Galaxy . "</div>";
		$this->retVal .= "<div class=\"systemName\">System: " . $systemProperties->Name . " [" . $systemProperties->Number . "]</div>";
		if ($shipPosition != null)
			$this->retVal .= "<div class=\"systemPosition\">[X/Y]: " . $shipPosition->X . "/" . $shipPosition->Y . "</div>";
		$this->retVal .= "<div class=\"sectorName\">" . TranslateController::getDefault()->get ( 'sector' ) . ": " . TranslateController::getDefault()->get ( $sectorProperties->Name ) . "</div>";
		$this->retVal .= "<div class=\"sectorOther\">" . TranslateController::getDefault()->get ( 'movecost' ) . ": " . $sectorProperties->MoveCost . "</div>";
		$this->retVal .= "<div class=\"sectorOther\">" . TranslateController::getDefault()->get ( 'visibility' ) . ": " . $sectorProperties->Visibility . "%</div>";
		$this->retVal .= "<div class=\"sectorOther\">" . TranslateController::getDefault()->get ( 'accuracy' ) . ": " . $sectorProperties->Accuracy . "%</div>";
		$this->retVal .= "</td>";
		$this->retVal .= "</tr>";
		$this->retVal .= "</table>";
		return true;
	}
}
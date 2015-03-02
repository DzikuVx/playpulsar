<?php

namespace Gameplay\Helpers;

class MapSector {
	public $bgColor = "000000";
	public $icon = "&nbsp;";
	public $iconColor = "ffffff";
	public $visibility = 100;
	public $shipCount = 0;
	public $showPercentage = 0;
	public $stackCount = 0;
	public $gfx = null;
	public $border = false;
	public $onClick = null;
	public $system;
	public $X;
	public $Y;
	public $Name = '';

	public function render($mapType = null) {

		global $config;

		if (empty($this->gfx)) {
			$retVal = "<td style=\"background-color: #000; color: #" . $this->iconColor . ";\" ";
		}
		else {
			$retVal = "<td title='{$this->Name}' style=\"background-color: #000; background-image:url('{$config['general']['cdn']}gfx/sectors/thumbs/{$this->gfx}'); color: #" . $this->iconColor . ";\" ";
		}

		if ($this->onClick != null) {
			$retVal .= "onclick=\"" . $this->onClick . "('{$this->system}','{$this->X}','{$this->Y}');\"";
		}

		$retVal .= " >";

		if ($mapType == 'Gameplay\Panel\MiniMap') {

			$oCacheKey = new \phpCache\CacheKey('mapSectorMarker', $this->system.'|'.$this->X.'|'.$this->Y);
            $oCache    = \phpCache\Factory::getInstance()->create();

			if (\additional::checkRand($this->showPercentage,100)) {
				$retVal .= '<img src="gfx/shipMarker.png" style="position: absolute; margin-top: -1px;" />';
				$oCache->set($oCacheKey, 1);
			} else {

				/*
				 * Marker że był kontakt
				 */
				$tMarker = $oCache->get($oCacheKey);
				if ($tMarker === 1) {
					$retVal .= '<img src="gfx/shipMarker.png" style="position: absolute; margin-top: -1px; opacity: 0.5;"/>';
					$oCache->set($oCacheKey, 0);
				}

			}
		}
		elseif ($mapType == 'Gameplay\Panel\ActiveScanner') {
			if (\additional::checkRand($this->showPercentage,100)) {
				$retVal .= '<img src="gfx/shipMarker.png" style="position: absolute; margin-top: -1px;" />';
			}
		}

		if ($this->border) {
			$retVal .= '<img src="gfx/csMarker.png" style="position: absolute; margin-top: -1px;" />';
		}

		$retVal .= '<span>'.$this->icon.'</span>';

		$retVal .= "</td>";
		return $retVal;
	}

}
<?php

namespace Gameplay\Panel;

abstract class BaseTable extends Renderable {
	
	/**
	 * @return string
	 */
	public function renderFooter() {
	
		return "</table></div>";
	}
	
}
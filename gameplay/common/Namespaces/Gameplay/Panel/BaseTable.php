<?php

namespace Gameplay\Panel;

abstract class BaseTable extends Base {
	
	/**
	 * @return string
	 */
	public function renderFooter() {
	
		return "</table></div>";
	}
	
}
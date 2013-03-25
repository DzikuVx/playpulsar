<?php

namespace Controlpanel;

class Welcome extends BaseItem{

	/**
	 * Browse
	 *
	 * @param user $user
	 * @param array $params
	 * @return string
	 */
	public function browse($user, $params) {

		return '<h1>Welcome na Pulsar Online Control Panel</h1>';
	}

}
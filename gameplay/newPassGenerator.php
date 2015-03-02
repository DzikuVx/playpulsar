<?php

// require_once 'common.php';

function __autoload($klasa) {
	if (file_exists ( dirname ( __FILE__ ) . '/common/classes/' . $klasa . '.php' )) {
		/**
		 * Klasy common
		 */

		require_once dirname ( __FILE__ ) . '/common/classes/' . $klasa . '.php';
	} elseif (file_exists ( dirname ( __FILE__ ) . '/engine/classes/' . $klasa . '.php' )) {

		/*
		 * Klasy silnika gry
		 */
		require_once dirname ( __FILE__ ) . '/engine/classes/' . $klasa . '.php';
	}
}
 
echo user::sPasswordHash('Romek', 'superpassword');
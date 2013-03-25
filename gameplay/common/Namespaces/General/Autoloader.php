<?php
namespace General;

/**
 * Universal PHP class loader with basic Namespace support
 * @author Paweł
 * @since 2012-04-17
 *
 */
class Autoloader {

	static public function loadClass($sClassName) {

		$sNamespaced = str_replace('\\', '/', $sClassName);

		$sBaseDir = dirname ( __FILE__ ).'/../../..';

		/*
		 * Autolodaer with Namespaces feature
		*/
		if (file_exists($sBaseDir.'/common/Namespaces/' . $sNamespaced . '.php')) {
			require_once $sBaseDir.'/common/Namespaces/' . $sNamespaced . '.php';
		}
		elseif (file_exists ( $sBaseDir.'/common/classes/' . $sClassName . '.php' )) {
			/**
			 * Klasy common
			 */

			require_once $sBaseDir.'/common/classes/' . $sClassName . '.php';
		}
		elseif (file_exists ( $sBaseDir.'/controlpanel/classes/' . $sClassName . '.php' )) {

			/*
			 * Klasy natywne control panel
			*/
			require_once $sBaseDir.'/controlpanel/classes/' . $sClassName . '.php';
		}
		elseif (file_exists ( $sBaseDir.'/engine/classes/' . $sClassName . '.php' )) {

			/*
			 * Klasy silnika gry
			*/
			require_once $sBaseDir.'/engine/classes/' . $sClassName . '.php';
		}

	}

	static function register() {
		spl_autoload_register("General\Autoloader::loadClass");
	}
	
	static function unregister() {
		spl_autoload_unregister("General\Autoloader::loadClass");
	}

}
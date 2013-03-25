<?php

namespace Cache;

/**
 * Kontroler cache
 * Static
 * @author Paweł
 *
 */
class Controller {

	/**
	 * Obiekt klasy cache
	 * @var mixed
	 */
	private static $cacheInstance;

	/**
	 * Konstruktor prywatny
	 */
	private function __construct() {

	}

	/*
	 * Metoda tworząca obiekt cache
	 */
	static private function create() {

		if (empty(self::$cacheInstance)) {

			global $config;

			if (!empty($config['useApc'])) {
				self::$cacheInstance = Apc::getInstance();
			}else {
				self::$cacheInstance = Variable::getInstance();
			}

		}

	}

	/**
	 * Pobranie obiektu klasy cacheującej
	 * @throws Exception
	 * @return Apc
	 */
	static public function getInstance() {

		if (empty(self::$cacheInstance)) {
			self::create();
		}

		if (empty(self::$cacheInstance)) {
			throw new \Exception('Cache object is not initialized');
		}

		return self::$cacheInstance;
	}

	static public function insertCacheClear($userID, $module) {

		try {

			$tQuery = "INSERT INTO cacheclear(UserID, Module) VALUES('{$userID}','{$module}')";
			\Database\Controller::getInstance()->execute ( $tQuery );

		} catch ( \Database\Exception $e ) {
			/**
			 * jeśli błąd inny od zduplikowanego klucza, rzuć wyjątkiem
			 */
			if ($e->getCode () != 1062) {
				throw new \Database\Exception ( $e->getMessage (), $e->getCode () );
			}
		}
	}

	/**
	 * Wystawienie polecenia oczyszczenia cache dla wybranych modułów
	 *
	 * @param int $userID
	 * @param string $module
	 * @return boolean
	 */
	static public function forceClear($userID, $module) {

		self::getInstance()->clear($module, $userID);
		//@todo kiedy przestanę korzystać z cache sesji, będzie można wywalić
		self::insertCacheClear($userID, $module);

		return true;
	}

}
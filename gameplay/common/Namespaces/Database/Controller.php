<?php

namespace Database;

class Controller {

	private function __construct() {
	}

	/**
	 * @var MySQLiWrapper
	 */
	private static $instance = null;

	/**
	 * @var MySQLiWrapper
	 */
	private static $chatInstance = null;

	/**
	 * @var MySQLiWrapper
	 */
	private static $portalInstance = null;

	/**
	 * @var MySQLiWrapper
	 */
	private static $backendInstance = null;

    /**
     * @return MySQLiWrapper
     * @throws \Exception
     */
    public static function getInstance() {

		if (empty(self::$instance)) {
			self::connect();
		}

		if (empty(self::$instance)) {
			throw new \Exception('Data Base object failed to initialize');
		}

		return self::$instance;

	}

	/**
	 * @throws \Exception
	 * @return \Database\MySQLiWrapper
	 */
	public static function getChatInstance() {

		if (empty(self::$chatInstance)) {
			self::chatConnect();
		}

		if (empty(self::$chatInstance)) {
			throw new \Exception('Chat Data Base object failed to initialize');
		}

		return self::$chatInstance;

	}

	/**
	 * Pobranie \Gameplay\Model\Allianceobiektu bazy danych portalu
	 * @throws \Exception
	 * @return \Database\MySQLiWrapper
	 */
	public static function getPortalInstance() {

		if (empty(self::$portalInstance)) {
			self::portalConnect();
		}

		if (empty(self::$portalInstance)) {
			throw new \Exception('Chat Data Base object failed to initialize');
		}

		return self::$portalInstance;

	}

	public static function getBackendInstance() {

		if (empty(self::$backendInstance)) {
			self::backendConnect();
		}

		if (empty(self::$backendInstance)) {
			throw new \Exception('Backend Data Base object failed to initialize');
		}

		return self::$backendInstance;

	}

	/**
	 * Połączenie z bazą danych gameplay
	 */
	private static function connect() {
		global $dbConfig;

		self::$instance = new MySQLiWrapper ( $dbConfig );

	}

	/**
	 * Połączenie z bazą danych chatu
	 */
	private static function chatConnect() {
		global $chatDbConfig;
		self::$chatInstance = new MySQLiWrapper ( $chatDbConfig );

	}

	/**
	 * Połączenie z bazą danych portalu
	 */
	private static function portalConnect() {
		global $portalDbConfig;
		self::$portalInstance = new MySQLiWrapper ( $portalDbConfig );

	}

	private static function backendConnect() {
		global $backendDbConfig;
		self::$backendInstance = new MySQLiWrapper ( $backendDbConfig );

	}

}
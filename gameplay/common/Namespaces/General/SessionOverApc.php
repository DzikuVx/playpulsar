<?php

namespace General;

class SessionOverApc {

	private static $instance;

	public static function create(){
		if (empty(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	private function __construct() {
		session_set_save_handler(
		array($this, "open"),
		array($this, "close"),
		array($this, "read"),
		array($this, "write"),
		array($this, "destroy"),
		array($this, "gc")
		);
	}

	public function open($savePath, $sessionName) {
		return true;
	}

	public function close() {
		return true;
	}

	public function read($id) {
		return apc_fetch('SessionOverApc_'.$id);
	}

	public function write($id, $data) {
		@apc_store ( 'SessionOverApc_'.$id , $data , 864000);
		return true;
	}

	public function destroy($id) {
		apc_delete('SessionOverApc_'.$id);

		return true;
	}

	/**
	 *
	 * Garbage collector
	 * @param int $maxlifetime
	 */
	public function gc($maxlifetime) {
		$iterator = new APCIterator('user');
		while ($tKey = $iterator->current()) {

			if (mb_strpos($tKey['key'],'SessionOverApc_') === false) {
				continue;
			}

			if (time() > $tKey['mtime'] + $maxlifetime) {
				apc_delete($tKey['key']);
			}

			$iterator->next();
		}
	}
}

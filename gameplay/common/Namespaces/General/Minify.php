<?php
namespace General;

/**
 *
 * Class serves as minify wrapper for building minified JS and CSS files.
 * Used by Portal and Gameplay
 * @desc Singleton class for Minify
 * @author Paweł
 * @link http://code.google.com/p/minify/
 */
class Minify {

	/**
	 * object instance
	 * @var Minify
	 */
	static private $instance = null;

	/**
	 *
	 * Enter description here ...
	 * @var array
	 */
	private $config = array();

	static public function getInstance() {

		if (empty(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * private constructor
	 */
	private function __construct() {
		$this->getConfig();
	}

	/**
	 * loads config file
	 */
	private function getConfig() {
		require dirname(__FILE__).'/../../../minify.inc.php';
		$this->config = $minify;
	}

	/**
	 * Returns files array for minify
	 * @return array
	 */
	public function getGroups() {

		$aRetVal = array();

		$sBase = dirname(__FILE__);

		foreach ($this->config['groups'] as $sKey => $aGroup) {
			$aRetVal[$sKey] = array();
			foreach ($aGroup as $sFile) {
					
				$sFile = $sBase.'/../../../'.$sFile;
					
				array_push($aRetVal[$sKey], $sFile);
			}
		}
		return $aRetVal;
	}

	public function getScript($sGroup) {
		$sRetVal = '';

		if (!isset($this->config['groups'][$sGroup])) {
			throw new \Exception('Unknown Minify group');
		}
		
		if (empty($this->config['enable'])) {
				
			foreach ($this->config['groups'][$sGroup] as $aGroup) {
				$sRetVal .= '<script type="text/javascript" src="{cdnUrl}'.$aGroup.'"></script>'."\n";
			}
		}
		else {
			$sRetVal .= '<script type="text/javascript" src="{cdnUrl}min/?g='.$sGroup.'"></script>'."\n";
		}

		return $sRetVal;
	}
	
	public function getCss($sGroup) {
		$sRetVal = '';

		if (!isset($this->config['groups'][$sGroup])) {
			throw new \Exception('Unknown Minify group');
		}
		
		if (empty($this->config['enable'])) {
			foreach ($this->config['groups'][$sGroup] as $aGroup) {
				$sRetVal .= '<link href="{cdnUrl}'.$aGroup.'" rel="stylesheet" />'."\n";
			}
		}
		else {
			$sRetVal .= '<link href="{cdnUrl}min/?g='.$sGroup.'" rel="stylesheet" />'."\n";
		}

		return $sRetVal;
	}

}
<?php

namespace General;
use \Cache\Controller as Cache;

class Templater {

	/**
	 * Template file path
	 *
	 * @var string
	 */
	protected $fileName;

	/**
	 * Template string
	 *
	 * @var string
	 */
	protected $template;

	protected $cacheModule = null;
	protected $cacheProperty = null;

	/**
	 * Translation class
	 * @var translation
	 */
	protected $translation = null;

	/**
	 * If template should be cached
	 * @var boolean
	 */
	static $useCache = false;

	/**
	 * Construct function
	 *
	 * @param string $fileName
	 * @param string $cacheModule
	 * @param string $cacheProperty
	 * @return boolean
	 */
	public function __construct($fileName, $translation = null) {

		$this->fileName = $fileName;
		$this->load ();

		$this->translation = $translation;

		if (empty ( $this->translation )) {
			$this->translation = \TranslateController::getDefault();
		}

		return true;
	}

	/**
	 * Template load
	 *
	 */
	protected function load() {

		$module = 'Templater::load';
		$property = md5(realpath('').'|'.$this->fileName);

		if (!self::$useCache || !Cache::getInstance()->check($module, $property)) {

			try {
				if (file_exists ( $this->fileName )) {

					$tFile = fopen ( $this->fileName, 'r' );

					flock ( $tFile, LOCK_SH );

					$this->template = fread ( $tFile, filesize ( $this->fileName ) );

					flock ( $tFile, LOCK_UN );
					fclose ( $tFile );

					Cache::getInstance()->set($module, $property, $this->template, 86400);

				} else {
					throw new Exception ( 'Brak pliku' );
				}
			} catch ( Exception $e ) {
				throw new Exception ( 'Błąd otwarcia szablonu' );
			}
		}else {
			$this->template = Cache::getInstance()->get($module, $property);
		}
	}

	/**
	 * Template reload
	 *
	 */
	public function reset() {

		$this->load ();
	}

	/**
	 * Adding new position to template
	 *
	 * @param mixed $key
	 * @param string $value
	 * @return boolean
	 */
	public function add($key, $value = null) {

		try {

			if ($value !== null) {
				$this->template = str_replace ( '{' . $key . '}', $value, $this->template );
			} else {
				foreach ( $key as $tKey => $tValue ) {
					
					if ($tValue === NULL) {
						$tValue = '';
					}
					
					$this->add ( $tKey, $tValue );
				}
			}
		} catch ( Exception $e ) {
			return false;
		}

		return true;
	}

	/**
	 * Conditional block removal
	 *
	 * @param string $key
	 */
	public function remove($key) {

		$this->template = preg_replace ( '!({C:' . $key . '}.*{/C:' . $key . '})!ms', '', $this->template );
	}

	/**
	 * Template render
	 *
	 * @return string
	 */
	public function get() {

		$this->template = preg_replace_callback ( '!({T:[^}]*})!', array ($this, 'translationReplacer' ), $this->template );

		$this->template = preg_replace ( '!({C:[^}]*})!', '', $this->template );
		$this->template = preg_replace ( '!({/C:[^}]*})!', '', $this->template );

		return $this->template;
	}

	/**
	 * Template translation parsing
	 *
	 * @param array $matches
	 * @return string
	 */
	protected function translationReplacer($matches) {

		$retval = $matches [1];
		$retval = mb_substr ( $retval, 3, - 1 );

		$retval = $this->translation->get ( $retval );

		return $retval;
	}

	/**
	 * __toString magic function
	 *
	 * @return string
	 */
	public function __toString() {

		return $this->get ();
	}

}
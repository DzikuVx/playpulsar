<?php

/**
 * Klasa panelu agentcji informacyjnej
 *
 * @version $Rev: 457 $
 * @package Engine
 *
 */
class newsAgencyPanel extends basePanel {

	private static $instance = null;
	
	/**
	* Konstruktor statyczny
	* @return newsAgencyPanel
	*/
	public static function getInstance(){
		if (empty(self::$instance)) {
			$className = __CLASS__;
	
			global $userProperties;
	
			self::$instance = new $className($userProperties->Language);
		}
		return self::$instance;
	}
	
	/**
	 * Nazwa DIV
	 *
	 * @var string
	 */
	protected $panelTag = "newsAgencyPanel";

	/**
	 * Wyrenderowanie
	 *
	 * @param stdClass $shipPosition
	 */
	public function render($shipPosition) {
		global $userProperties;

		$oCacheKey = new \Cache\CacheKey('newsAgency::render', $shipPosition->System . '|' . $userProperties->Language);
		
		if (! \Cache\Controller::getInstance()->check ( $oCacheKey )) {

			$tQuery = " SELECT newsagency . *
        FROM 
          newsagency
        JOIN 
          newsagencytypes ON newsagencytypes.ID = newsagency.Type
        WHERE 
          newsagency.System = '{$shipPosition->System}'
          AND NewsagencyID >0
          AND newsagencytypes.Visible = 'yes'
        ORDER BY 
          NewsagencyID DESC
		    LIMIT 5
		    ";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			$tFound = false;
			$tValue = '';
			while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

				try {

					$tFound = true;
					$tObject = unserialize ( $tResult->Text );
					$tValue .= $tObject->render ( true );
					$tObject->doSave = false;
					unset ( $tObject );

				}catch (Exception $e) {
					psDebug::cThrow(null, $e, array('display'=>false));
				}

			}
			$this->retVal .= $tValue;
			\Cache\Controller::getInstance()->set ( $oCacheKey, $tValue, 3600 );
		} else {
			$tValue = \Cache\Controller::getInstance()->get ( $oCacheKey );
			if (empty ( $tValue )) {
				$tFound = false;
			} else {
				$tFound = true;
			}
			$this->retVal .= $tValue;
		}
		/**
		 * Jeśli nic nie ma, wyczyść panel
		 */
		if (! $tFound) {
			$this->clear ();
		}

	}

}
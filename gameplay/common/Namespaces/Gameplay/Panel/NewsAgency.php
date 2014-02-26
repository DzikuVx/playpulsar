<?php

namespace Gameplay\Panel;

use Gameplay\Model\ShipPosition;
use Interfaces\Singleton;

/**
 * @deprecated
 */
class NewsAgency extends Renderable implements Singleton {

	/**
	 *
	 * @var NewsAgency
	 */
	private static $instance = null;

	/**
	 * @return \Gameplay\Panel\NewsAgency
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			$className = __CLASS__;

            $userProperties = \Gameplay\PlayerModelProvider::getInstance()->get('UserEntity');
			self::$instance = new $className($userProperties->Language);
		}
		return self::$instance;
	}

	protected $panelTag = "NewsAgency";

    /**
     * @param ShipPosition $shipPosition
     */
    public function render(ShipPosition $shipPosition) {
        $userProperties = \Gameplay\PlayerModelProvider::getInstance()->get('UserEntity');

		$oCacheKey = new \phpCache\CacheKey('newsAgency::render', $shipPosition->System . '|' . $userProperties->Language);
        $oCache    = \phpCache\Factory::getInstance()->create();

		if (! $oCache->check ( $oCacheKey )) {

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
                    LIMIT 5";
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

				}catch (\Exception $e) {
					\psDebug::cThrow(null, $e, array('display'=>false));
				}

			}
			$this->retVal .= $tValue;
			$oCache->set ( $oCacheKey, $tValue, 3600 );
		} else {
			$tValue = $oCache->get ( $oCacheKey );
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
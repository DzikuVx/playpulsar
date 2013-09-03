<?php

namespace Controlpanel;

class ShipTypes extends GameplayItem{

	protected $detailTitle = 'Ship Data';
	protected $editTitle = '';
	protected $addTitle = '';

	protected $tableName = 'shiptypes';
	protected $tableIdField = 'ShipID';
	
	/**
	 * Edycja, wykonanie
	 * @param user $user
	 * @param array $params
	 * @throws customException
	 */
	public function editExe($user, $params) {
		$retVal = '';
	
		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}
	
		$tData = $this->getDataObject($params['id']);
	
		$tFields = array();
		foreach ($tData as $tKey => $tValue) {
			if ($tKey != 'ShipID') {
				array_push($tFields, $tKey);
			}
		}
	
		$tQuery = BaseItem::sMakeUpdateQuery('shiptypes', 'ShipID', $tFields, $params);
		\Database\Controller::getInstance()->execute($tQuery);
		\Cache\Controller::getInstance()->clear('ship',$params['id']);
	
		\General\Controls::reloadWithMessage(\General\Session::get('returnLink'), "Data has been <strong>set</strong>", 'success');
		
		return $retVal;
	}
	
	protected function getDataObject($itemID) {

		$tQuery = \Database\Controller::getInstance()->execute ( "
				SELECT 
					*
				FROM 
					shiptypes 
				WHERE 
					ShipID='{$itemID}' LIMIT 1" );
		return \Database\Controller::getInstance()->fetch ( $tQuery );
	}

	static private function sStationListData($id) {
		$retVal = array();

		$tQuery = "SELECT
					porttypes.PortTypeID,
					porttypes.NameEN,
					ports.System,
					ports.X,
					ports.Y
				FROM
					porttypes JOIN ports USING(PortTypeID)
				WHERE
					porttypes.Type='station' AND
					CONCAT(',',porttypes.Ships,',') LIKE '%,{$id},%'
		";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);
		while($tResult = \Database\Controller::getInstance()->fetch($tQuery)) {
			$tArray = array();
			$tArray['Id'] = $tResult->PortTypeID;
			$tArray['Name'] = $tResult->NameEN.' - '.$tResult->System.'/'.$tResult->X.'/'.$tResult->Y;
			array_push($retVal, $tArray);
		}

		return $retVal;
	}

	/**
	 * Pobranie tablicy stacji w których jest dostępny dany item
	 * @param int $id
	 * @param bool $cacheAble
	 * @return array
	 */
	static public function sGetStationList($id, $cacheAble = true) {

		$retVal = array();

		$oCacheKey = new \Cache\CacheKey('cpShipTypes::sGetStationList', $id);
		
		if ($cacheAble && \Cache\Controller::getInstance()->check($oCacheKey)) {
			$retVal = \Cache\Controller::getInstance()->get($oCacheKey);
		}else {
			$retVal = self::sStationListData($id);
			\Cache\Controller::getInstance()->set($oCacheKey, $retVal);
		}

		return $retVal;
	}

	protected function getAdditionalData($user, $params) {
		$retVal = '';

		$retVal .= '<h2>Avaible at Stations</h2>';

		$tArray = self::sGetStationList($params['id'], false);

		$retVal .= \General\Controls::sBuilUl($tArray);

		return $retVal;
	}

}
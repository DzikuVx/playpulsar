<?php

namespace Controlpanel;

class ProductTypes extends GameplayItem{

	protected $detailTitle = 'Goods Data';
	protected $editTitle = '';
	protected $addTitle = '';

	protected $tableName = 'products';
	protected $tableIdField = 'ProductID';

	public function editExe($user, $params) {
		$retVal = '';

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$tData = $this->getDataObject($params['id']);

		$tFields = array();
		foreach ($tData as $tKey => $tValue) {
			if ($tKey != 'ProductID') {
				array_push($tFields, $tKey);
			}
		}

		$tQuery = BaseItem::sMakeUpdateQuery('products', 'ProductID', $tFields, $params);
		\Database\Controller::getInstance()->execute($tQuery);
		\phpCache\Factory::getInstance()->create()->clear('product',$params['id']);
		\General\Controls::reloadWithMessage(\General\Session::get('returnLink'), "Data has been <strong>set</strong>", 'success');
	}

	protected function getDataObject($itemID) {

		$tQuery = \Database\Controller::getInstance()->execute ( "
				SELECT 
					products.*
				FROM 
					products
				WHERE 
					ProductID='{$itemID}' LIMIT 1" );
		return \Database\Controller::getInstance()->fetch ( $tQuery );
	}

}
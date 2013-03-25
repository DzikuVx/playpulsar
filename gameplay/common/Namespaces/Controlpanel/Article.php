<?php

namespace Controlpanel;

class Article extends News{

	protected $tableType = 'article';
	protected $templateFileName = 'templates/portalArticle.html';

	protected function clearCache($language) {
		\Cache\Controller::getInstance()->clear('menuNavigator::sRender', $language);
	}

	protected function addAdditionalData($user, $params, $template) {

		$tArray = array();
		$tArray['yes'] = 'Yes';
		$tArray['no'] = 'No';
		$template->add('Published', \General\Controls::renderSelect('Published', 'no',$tArray));

		return true;
	}

	protected function editAdditionalData($user, $params, $template) {

		$tArray = array();
		$tArray['yes'] = 'Yes';
		$tArray['no'] = 'No';
		$template->add('Published', \General\Controls::renderSelect('Published', $this->dataObject->Published, $tArray));


		return true;
	}

}
<?php

namespace Controlpanel;

use General\Templater;

class Article extends News {

	protected $tableType = 'article';
	protected $templateFileName = 'templates/portalArticle.html';

	protected function clearCache($language) {
		\phpCache\Factory::getInstance()->create()->clear(new \phpCache\CacheKey('menuNavigator::sRender', $language));
	}

    /**
     * @param $user
     * @param array $params
     * @param Templater $template
     * @return bool
     */
    protected function addAdditionalData($user, $params, $template) {

		$tArray = array();
		$tArray['yes'] = 'Yes';
		$tArray['no'] = 'No';
		$template->add('Published', \General\Controls::renderSelect('Published', 'no',$tArray));

		return true;
	}

    /**
     * @param $user
     * @param array $params
     * @param Templater $template
     * @return bool
     */
    protected function editAdditionalData($user, $params, $template) {

		$tArray = array();
		$tArray['yes'] = 'Yes';
		$tArray['no'] = 'No';
		$template->add('Published', \General\Controls::renderSelect('Published', $this->dataObject->Published, $tArray));

		return true;
	}

}
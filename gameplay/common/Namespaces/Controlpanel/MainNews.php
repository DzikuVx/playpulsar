<?php

namespace Controlpanel;

class MainNews extends News{

	protected $templateFileName = 'templates/portalMainNews.html';

	protected function clearCache($language) {
		\phpCache\Factory::getInstance()->create()->clear('portalMainNews', $language);
	}

}

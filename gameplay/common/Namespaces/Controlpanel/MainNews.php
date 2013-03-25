<?php

namespace Controlpanel;

class MainNews extends News{

	protected $templateFileName = 'templates/portalMainNews.html';

	protected function clearCache($language) {
		\Cache\Controller::getInstance()->clear('portalMainNews', $language);
	}

}

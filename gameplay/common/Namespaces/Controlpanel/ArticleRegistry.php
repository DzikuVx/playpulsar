<?php

namespace Controlpanel;

class ArticleRegistry extends NewsRegistry {

	protected $itemClass = "\Controlpanel\Article";
	protected $extraList = "portal_news.Type='article' AND portal_news.MainNews = 'no'";
	protected $registryTitle = "Portal articles";

}
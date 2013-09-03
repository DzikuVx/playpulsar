<?php

namespace Portal\Rss;

class Aggregator {

	protected $rssURI = '';

	protected $xmlObject = null;

	protected $uriCache = false;

	protected $uriContent = '';

	protected $cacheValidThreshold = 3600;

	protected $renderItemLimit = 10;

	protected $listClassName = 'psRssAgregator';

	/**
	 * Sets renderer function output items number limit
	 * @param unknown_type $value
	 */
	public function setRenderItemLimit($value) {
		$this->renderItemLimit = $value;
	}

	/**
	 * Konstruktor
	 * @param string $uri
	 * @param boolean $cacheable
	 */
	public function __construct($uri, $cacheable = false, $cacheValidThreshold = 3600) {
		$this->rssURI = $uri;
		$this->uriCache = $cacheable;
		$this->cacheValidThreshold = $cacheValidThreshold;
		$this->open();
	}

	/**
	 * Pobranie treści z feedu
	 */
	protected function loadContent() {
		$this->uriContent = file_get_contents($this->rssURI);
	}

	/**
	 * RSS open
	 */
	protected function open() {

		if (empty($this->uriCache)) {
			$this->loadContent();
		}else {
			try {

				$oCacheKey = new \Cache\CacheKey('psRssAgregator::open', md5($this->rssURI));
				
				if (!\Cache\Controller::getInstance()->check($oCacheKey)) {
					$this->loadContent();
					\Cache\Controller::getInstance()->set($oCacheKey, $this->uriContent, $this->cacheValidThreshold);
				}else {
					$this->uriContent = \Cache\Controller::getInstance()->get($oCacheKey);
				}

			}catch (Exception $e) {
				psDebug::cThrow(null, $e, array('display'=>false));
				$this->loadContent();
			}
		}

		$this->xmlObject = new SimpleXMLElement($this->uriContent);

	}

	public function getChannel() {
		return $this->xmlObject->channel;
	}

	/**
	 * gets channel title
	 * @return string
	 */
	public function getTitle() {
		return (string) $this->getChannel()->title;
	}

	public function __toString() {

		$retVal = '';

		$tData = $this->getChannel();

		$retVal .= '<ul class="'.$this->listClassName.'">';

		foreach ($tData->item as $tItem) {
			$retVal .= '<li>';
			$retVal .= '<a href="'.$tItem->link.'">';
			$retVal .= $tItem->title;
			$retVal .= '</a>';
			$retVal .= '</li>';
		}

		$retVal .= '</ul>';

		return $retVal;
	}

	/**
	 * Public destrutor
	 */
	public function destoy(){
		unset ($this);
	}

}
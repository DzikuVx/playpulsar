<?php

namespace Portal;

use General\Templater;

class Article extends News{

	static public function sGenUrl($id, $title) {
		//		return '?class=portalArticle&method=detail&id='.$id;
		return 'entry_' . $id . '_' . urlencode ( mb_substr ( $title, 0, 64 ) ) . '_.html';
	}

    /**
     * @param array $params
     * @param Templater $template
     * @return string
     */
    public function detail($params, $template) {

		$retVal = '';

		$tObject = new self();
		$tObject->get($params['id']);
		$retVal .= $tObject->render(null, false, false, false);

		/*
		 * Jeśli wywołany ze strony głównej, nadpisz tytuł strony
		 */
		if ($params['class'] == '\Portal\Article' && $params['method'] == 'detail' ) {
			$template->add('pageTitle', $tObject->getDataObject()->Title);
		}

		return $retVal;
	}

    /**
     * @param Article $object
     * @param bool $renderNav
     * @return null|string
     */
    public function render($object = null, $renderNav = false) {
		if (empty($object)) {
			/**
			 * @var \Portal\Article
			 */
			$object = $this->dataObject;
		}
		if (empty($object)) {
			return null;
		}
	
		$retVal = '';
	
		$retVal .= "<h1>";
		$retVal .= $object->Title;
		$retVal .= "</h1>";
	
		$retVal .= "<div>" . $object->Text . "</div>";
	
		return $retVal;
	}
	
}
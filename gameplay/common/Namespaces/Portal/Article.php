<?php

namespace Portal;

class Article extends News{

	static public function sGenUrl($id, $title) {
		//		return '?class=portalArticle&method=detail&id='.$id;
		return 'entry_' . $id . '_' . urlencode ( mb_substr ( $title, 0, 64 ) ) . '_.html';
	}

	/**
	 * (non-PHPdoc)
	 * @see Portal.News::detail()
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
	 * (non-PHPdoc)
	 * @see Portal.News::render()
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
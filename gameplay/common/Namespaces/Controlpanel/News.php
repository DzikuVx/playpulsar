<?php

namespace Controlpanel;

use \Database\Controller as Database;
use \General\Controls as Controls;

class News extends BaseItem {

	protected $tableName = 'portal_news';
	protected $tableIdField = 'NewsID';
	protected $templateFileName = 'templates/portalNews.html';

	protected $tableType = 'news';

	protected function assignDbObject() {
		$this->db = Database::getPortalInstance();
	}

	/**
	 * Wyświetlenie szczegłów newsa
	 * @param array $user
	 * @param array $params
	 * @return string
	 * @see portalNews::render()
	 */
	public function detail($user, $params) {

		$tObject = new \Portal\News();
		$tObject->get($params['id']);

		$retVal = '<h1>Message detail</h1>';
		$retVal .= '<p>';
		$retVal .= Controls::bootstrapButton('Close','window.history.back();','btn-inverse','icon-off');
		$retVal .= Controls::bootstrapButton('Edit', "document.location='?class=".get_class($this)."&amp;method=edit&amp;id={$params['id']}'",'btn-warning','icon-pencil');
		$retVal .= '</p>';
		$retVal .= $tObject->render(null, false);

		return $retVal;
	}

	protected function clearCache($language) {
		Cache::getInstance()->clear(new CacheKey('newsRegistry', $language));
	}

	/**
	 *
	 * Zapisanie newsa do bazy danych
	 * @param user $user
	 * @param array $params
	 * @return string
	 * @since 2010-08-11
	 */
	public function addExe($user, $params) {

		$params['text'] = $this->db->quote($params['text']);
		$params['title'] = $this->db->quote($params['title']);

		if (empty($params['Published'])) {
			$params['Published'] = 'yes';
		}

		$tQuery = "INSERT INTO {$this->tableName}(
				Published,
				Language,
				MainNews,
				UserID,
				UserName,
				Time,
				Title,
				Text,
				Type
			) VALUES(
				'{$params['Published']}',
				'{$params['Language']}',
				'no',
				'{$_SESSION ['cpLoggedUserID']}',
				'{$_SESSION ['cpLoggedUserName']}',
				'".time()."',
				'{$params['title']}',
				'{$params['text']}',
				'{$this->tableType}'
			)";

		$this->db->execute($tQuery);

		global $config;

		$retVal = Controls::reloadWithMessage($config['backend']['fileName'].'?class='.get_class($this).'&method=browse', 'Item created');

		$this->clearCache($params['Language']);

		return $retVal;
	}

	/**
	 * Formularz dodawania newsa
	 * @param user $user
	 * @param array $params
	 * @return string
	 */
	public function add($user, $params) {

		$retVal = '';

		$template = new \General\Templater($this->templateFileName);

		$template->add('title','New portal news');
		$template->add('Text', Controls::renderInput('html','','text'));
		$template->add('Title', Controls::renderInput('text','','title','text',128));

		$tArray = array();
		$tArray['pl'] = 'Polish';
		$tArray['en'] = 'English';
		$template->add('Language', Controls::renderSelect('Language', '',$tArray));

		$this->addAdditionalData($user, $params, $template);

		$retVal .= $this->openForm();
		$retVal .= (string) $template;
		$retVal .= Controls::renderInput('hidden',get_class($this),'class');
		$retVal .= Controls::renderInput('hidden','addExe','method');
		$retVal .= $this->closeForm();
		$retVal .= "<p>";
		$retVal .= Controls::bootstrapButton ( "Save", "document.myForm.onsubmit();",'btn-primary','icon-ok');
		$retVal .= Controls::bootstrapButton ( "Cancel", "window.history.back();",'btn-inverse' ,'icon-off');
		$retVal .= "</p>";

		return  $retVal;
	}

	protected function addAdditionalData($user, $params, $template) {
		return true;
	}

	/**
	 * @since 2010-12-05
	 * @param user $user
	 * @param array $params
	 */
	public function edit($user, $params) {

		$retVal = '';

		$this->loadDataObject($params['id']);

		$template = new \General\Templater($this->templateFileName);

		$template->add('title','Portal news edit');
		$template->add('Text', Controls::renderInput('html', $this->dataObject->Text,'text'));
		$template->add('Title', Controls::renderInput('text', $this->dataObject->Title, 'title','text',128));

		$tArray = array();
		$tArray['pl'] = 'Polish';
		$tArray['en'] = 'English';
		$template->add('Language', Controls::renderSelect('Language', $this->dataObject->Language, $tArray, array('class'=>'ui-state-default ui-corner-all')));

		$this->editAdditionalData($user, $params, $template);

		$retVal .= $this->openForm();
		$retVal .= (string) $template;
		$retVal .= Controls::renderInput('hidden',get_class($this),'class');
		$retVal .= Controls::renderInput('hidden',$params['id'],'id');
		$retVal .= Controls::renderInput('hidden','editExe','method');
		$retVal .= $this->closeForm();
		$retVal .= "<p>";
		$retVal .= Controls::bootstrapButton ( "Save", "document.myForm.onsubmit();",'btn-primary','icon-ok');
		$retVal .= Controls::bootstrapButton ( "Cancel", "window.history.back();" ,'btn-inverse' ,'icon-off');
		$retVal .= "</p>";

		return  $retVal;
	}

	protected function editAdditionalData($user, $params, $template) {
		return true;
	}

	/**
	 * @param user $user
	 * @param array $params
	 * @since 2010-12-05
	 */
	public function editExe($user, $params) {

		global $config;

		if (empty($params['title'])) {
			$params['title'] = '';
		}

		$params['text'] = $this->db->quote($params['text']);
		$params['title'] = $this->db->quote($params['title']);

		if (empty($params['Published'])) {
			$params['Published'] = 'yes';
		}

		$tQuery = "UPDATE {$this->tableName} SET
				Language = '{$params['Language']}',
				Title = '{$params['title']}',
				Text = '{$params['text']}',
				Published = '{$params['Published']}'
			WHERE
		{$this->tableIdField}='{$params['id']}'
			";

		$this->db->execute($tQuery);

		$this->clearCache($params['Language']);

		/*
		 * I wyczyść cache poszczególnych plików
		*/
		Cache::getInstance()->clear(new CacheKey('portalNews::get',$params['id']));

		$retVal = Controls::reloadWithMessage($config['backend']['fileName'].'?class='.get_class($this).'&method=browse', 'Item modified');

		return $retVal;
	}

}

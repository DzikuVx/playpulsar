<?php

namespace Controlpanel;

use \General\Controls as Controls;

class GameplayItem extends BaseItem {

	protected $detailTitle = '';
	protected $editTitle = '';
	protected $addTitle = '';

	protected function getAdditionalData($user, $params) {
		return '';
	}

	public function edit($user, $params) {

		$tData = $this->getDataObject($params['id']);

		$retVal = '';
		$retVal .= Controls::sOpenForm(get_class($this));
		$retVal .= Controls::sBuilEditTable($tData, 2);
		$retVal .= Controls::renderInput('hidden',get_class($this),'class');
		$retVal .= Controls::renderInput('hidden',$params['id'],'id');
		$retVal .= Controls::renderInput('hidden','editExe','method');
		$retVal .= Controls::sCloseForm();
		$retVal .= "<p>";
		$retVal .= Controls::bootstrapButton ( "Save", "document.myForm.onsubmit();",'btn-primary','icon-ok');
		$retVal .= Controls::bootstrapButton ( "Cancel", "window.history.back();",'btn-inverse' ,'icon-off');
		$retVal .= "</p>";

		return $retVal;
	}

	public function detail($user, $params) {

		$_SESSION['returnLink'] = $_SERVER['REQUEST_URI'];
		$_SESSION['returnUser'] = $params['id'];

		$template = new \General\Templater('templates/itemDetail.html');

		$template->add('Title',$this->detailTitle);

		$tData = $this->getDataObject($params['id']);

		$tString = Controls::sBuilTable($tData,3);

		$tString .= $this->getAdditionalData($user, $params);

		$template->add('Data',$tString);

		$template->add('CLOSE_BUTTON',Controls::bootstrapButton ( "Close", "document.location='index.php?class=".get_class($this)."&method=browse'",'btn-inverse', 'icon-off' ));

		if (\user::sGetRole() == 'admin') {
			$template->add('editButton',Controls::bootstrapButton ( "Edit", "document.location='index.php?class=".get_class($this)."&method=edit&id={$params['id']}'", 'btn-warning','icon-pencil' ));
			$template->add('deleteButton',Controls::bootstrapButton ( "Delete", "document.location='index.php?class=".get_class($this)."&method=delete&id={$params['id']}'", 'btn-danger','icon-trash' ));
		}else {
			$template->remove('operations');
		}

		return (string)$template;
	}

}
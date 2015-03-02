<?php

namespace Controlpanel;
use \General\Controls as Controls;

class Abusement extends BaseItem {

	/**
	 * (non-PHPdoc)
	 * @see Controlpanel.BaseItem::getDataObject()
	 */
	protected function getDataObject($itemID) {

		$sQuery = $this->db->execute ( "
				SELECT 
					abusements.*
				FROM 
					abusements JOIN users AS user ON user.UserID=abusements.UserID
					JOIN users AS by_user ON by_user.UserID=abusements.ByUserID
				WHERE 
					AbusementID='{$itemID}' 
				LIMIT 1" );
		return $this->db->fetch ( $sQuery );
	}

	public function detail(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {

		
		$_SESSION['returnLink'] = $_SERVER['REQUEST_URI'];
		$_SESSION['returnUser'] = $params['id'];

		$template = new \General\Templater('templates/itemDetail.html');

		$template->add('Title','Abusement report');

		$tData = $this->getDataObject($params['id']);

		$tString = Controls::sBuilTable($tData,3);

		$template->add('Data',$tString);

		$template->add('CLOSE_BUTTON',Controls::bootstrapButton ( "Close", "document.location='index.php?class=".get_class($this)."&method=browse'", 'btn-inverse','icon-off' ));

		if (\user::sGetRole() == 'admin') {
			$template->add('editButton',Controls::bootstrapButton ( "Edit", "document.location='index.php?class=".get_class($this)."&method=edit&id={$params['id']}'", 'btn-warning','icon-pencil' ));
			$template->add('deleteButton',Controls::bootstrapButton ( "Delete", "document.location='index.php?class=".get_class($this)."&method=delete&id={$params['id']}'", 'btn-danger', 'icon-trash' ));
		}else {
			$template->remove('operations');
		}

		return (string)$template;
	}

}
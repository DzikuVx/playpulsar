<?php

namespace Controlpanel;
use \General\Controls as Controls;

class NpcName extends BaseItem {

	protected $tableName = 'names';
	protected $tableIdField = 'ID__';
	protected $templateFileName = 'templates/npcName.html';

	protected $tableType = 'names';

	/**
	 * New NPC Name form
	 * @param user $user
	 * @param array $params
	 */
	public function add($user, $params) {

		$retVal = '';

		$template = new \General\Templater($this->templateFileName);

		$template->add('title','New NPC Name');
		$template->add('Name', Controls::renderInput('text','','Name','text',64));

		$tArray = array();
		$tArray['first'] = 'First';
		$tArray['last'] = 'Last';
		$template->add('Type', Controls::renderSelect('Type', '',$tArray));

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

	/**
	 * New NPC name, save to db and confirm
	 * @param user $user
	 * @param array $params
	 * @throws \Exception
	 */
	public function addExe($user, $params) {

		$params['Type'] = $this->db->quote($params['Type']);
		$params['Name'] = $this->db->quote($params['Name']);

		$tQuery = "INSERT INTO {$this->tableName}(
					Type,
					Name
				) VALUES(
					'{$params['Type']}',
					'{$params['Name']}'
				)";

		try {

			$this->db->execute($tQuery);

		}catch (\Exception $e) {

			if ($e->getCode() == 1062) {

				//@todo some kind of nice message with return link
				echo \psDebug::halt('Duplicate Entry',null,array('trace'=>false,'send'=>false));

			}else {
				throw new \Exception($e->getMessage(), $e->getCode(), $e);
			}

		}

		global $config;
		$retVal = Controls::reloadWithMessage($config['backend']['fileName'].'?class='.get_class($this).'&method=browse', 'Item created');
		
		return $retVal;
	}

	public function edit($user, $params) {

		$retVal = '';

		$this->loadDataObject($params['id']);

		$template = new \General\Templater($this->templateFileName);

		$template->add('title','Edit NPC Name');
		$template->add('Name', Controls::renderInput('text', $this->dataObject->Name, 'Name','text',64));

		$tArray = array();
		$tArray['first'] = 'First';
		$tArray['last'] = 'Last';
		$template->add('Type', Controls::renderSelect('Type', $this->dataObject->Type, $tArray));

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

	public function editExe($user, $params) {

		$params['Name'] = $this->db->quote($params['Name']);
		$params['Type'] = $this->db->quote($params['Type']);

		$tQuery = "UPDATE {$this->tableName} SET
					Type = '{$params['Type']}',
					Name = '{$params['Name']}'
				WHERE
		{$this->tableIdField}='{$params['id']}'
				";

		try {

			$this->db->execute($tQuery);

		}catch (\Exception $e) {

			if ($e->getCode() == 1062) {

				//@todo some kind of nice message with return link
				echo \psDebug::halt('Duplicate Entry',null,array('trace'=>false,'send'=>false));

			}else {
				throw new \Exception($e->getMessage(), $e->getCode(), $e);
			}

		}

		global $config;
		Controls::reloadWithMessage($config['backend']['fileName'].'?class='.get_class($this).'&method=browse', 'Item modified');
	}
}
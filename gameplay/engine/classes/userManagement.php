<?php
/**
 * Zarządzanie graczami
 *
 * @version $Rev: 455 $
 * @package Engine
 */
class userManagement {

	/**
	 * Utworzenie nowego użytkownika
	 *
	 */
	static public function create() {
		global $config;

		/**
		 * Dane użytkownika, tabela users
		 */

		$params = $_POST;
		\Database\Controller::getInstance()->quoteAll($params);

        $tUsers = new \Gameplay\Model\UserEntity();
		$tUsers->Password = user::sPasswordHash($params ['Login'], $params ['Password'] );
		$tUsers->Login = $params ['Login'];
		$tUsers->Email = $params ['Email'];
		$tUsers->Name = $params ['Name'];
		$tUsers->UserLocked = 'no';
		$tUsers->UserActivated = 'no';
		$tUsers->Country = $params ['Country'];
		$tUsers->Language = $params ['Language'];
		$tUsers->About = $params ['About'];
		$tUsers->AllowSpam = $params ['AllowSpam'];
		$tUsers->Type = 'player';
		$tUsers->NPCTypeID = null;

		$userID = $tUsers->insert();

		/*
		 * Wstaw pozycję użytkownika
		 */
        $position = new \Gameplay\Model\ShipPosition();
		$position->System = additional::randFormList ( $config ['userDefault'] ['system'] );
		$position->X = 0;
		$position->Y = 0;
		$position->Docked = "yes";

		/**
		 * Pozycja startowa, jeden z portów w systemie
		 */
		$tPosition = \Gameplay\Model\SystemProperties::randomPort ( $position );

        $tItem = new \Gameplay\Model\ShipPosition();
		$tItem->UserID = $userID;
		$tItem->System = $position->System;
		$tItem->X = $tPosition->X;
		$tItem->Y = $tPosition->Y;
		$tItem->Docked = 'yes';

        $tItem->insert();
	}

}
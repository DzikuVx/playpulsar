<?php
/**
 * Klasa wyniku strzelania pojedynczej broni
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class weaponFireResult {
	/**
	 * Kontent (firer, target)
	 *
	 * @var string
	 */
	private $content = 'player';

	/**
	 * Statek strzelający
	 *
	 * @var string
	 */
	private $firingShipName = '';

	/**
	 * Statek strzelany
	 *
	 * @var string
	 */
	private $targetShipName = '';

	/**
	 * Nazwa broni
	 *
	 * @var int
	 */
	private $weaponID = null;

	/**
	 * Typ zdarzenia
	 *
	 * @var string
	 */
	private $type = '';

	/**
	 * Uszkodzenie osłon
	 *
	 * @var int
	 */
	private $shdDamage = null;

	/**
	 * uszkodzenie pancerza
	 *
	 * @var int
	 */
	private $armDamage = null;

	/**
	 * Drenaż energii
	 *
	 * @var int
	 */
	private $powDamage = null;

	/**
	 * Uszkodzenie emp
	 *
	 * @var int
	 */
	private $empDamage = null;

	private $timeStamp;

	/**
	 * Przygotowanie formy do zapisu do bazy danych
	 *
	 * @param weaponFireResult $data
	 * @return string
	 */
	static private function sEncodeSaveModel($data) {
		return serialize ( $data );
	}

	/**
	 * Odtworzenie obiektu
	 *
	 * @param string $data
	 * @return weaponFireResult
	 */
	static public function sDecodeSaveModel($data) {
		return unserialize ( $data );
	}

	/**
	 * Konstruktor publiczny
	 *
	 * @param string $firingShipName
	 * @param string $targetShipName
	 * @param int $weaponID
	 * @param string $type
	 * @param int $shdDamage
	 * @param int $armDamage
	 * @param int $powDamage
	 * @param int $empDamage
	 */
	public function __construct($content, $firingShipName, $targetShipName, $weaponID, $type, $shdDamage = null, $armDamage = null, $powDamage = null, $empDamage = null) {
		$this->weaponID = $weaponID;
		$this->content = $content;
		$this->firingShipName = $firingShipName;
		$this->targetShipName = $targetShipName;
		$this->type = $type;
		$this->shdDamage = $shdDamage;
		$this->armDamage = $armDamage;
		$this->powDamage = $powDamage;
		$this->empDamage = $empDamage;
		$this->timeStamp = time();
	}

	/**
	 * Zapisanie raportów walki do bazy danych
	 *
	 * @param int $userID - którego dotyczczy
	 * @param int $byUserID - kto strzelił
	 * @param string $Type - defensive/offensive
	 * @return boolean
	 */
	public function save($userID, $byUserID, $type = 'defensive', $preparedQuery) {

		$time = time();
		$content = self::sEncodeSaveModel ( $this );

		mysqli_stmt_bind_param($preparedQuery, 'iiiss', $time, $userID, $byUserID, $content, $type);
		$tResult = mysqli_stmt_execute($preparedQuery);

		if (empty($tResult)) {
			throw new \Database\Exception ( mysqli_error (\Database\Controller::getInstance()->getHandle()), mysqli_errno (\Database\Controller::getInstance()->getHandle()) );
		}

		return true;
	}

	/**
	 * Wyrenderowanie
	 *
	 * @param translation $t
	 * @param string $language
	 * @return string
	 */
	public function render($translate, $language = 'pl') {

		$retVal = '';
		if (! empty ( $this->weaponID )) {
			$tWeapon = weapon::quickLoad ( $this->weaponID );

			if ($language == 'pl') {
				$tWeapon->Name = $tWeapon->NamePL;
				$tWeapon->ClassName = $tWeapon->ClassNamePL;
			} else {
				$tWeapon->Name = $tWeapon->NameEN;
				$tWeapon->ClassName = $tWeapon->ClassNameEN;
			}
		}

		$tTimeDiff = time()-$this->timeStamp;

		switch ($this->content) {

			case 'target' :
					
				switch ($this->type) {

					case 'disengage' :
						$retVal .= '<div>' . $this->firingShipName . ' - ' . $translate->get ( 'disengagedFromCombat' ) . '</div>';
						break;
							
					case 'noPower' :
						$retVal .= '<div style="color: aqua;">' . $this->firingShipName . ' - ' . $translate->get ( 'noPowerToFire' ) . $tWeapon->Name . '</div>';
						break;
							
					case 'noAmmo' :
						$retVal .= '<div style="color: aqua;">' . $this->firingShipName . ' - ' . $translate->get ( 'noAmmoToFire' ) . $tWeapon->Name . '</div>';
						break;
							
					case 'miss' :
						$retVal .= '<div style="color: #FF00FF;">' . $this->firingShipName . ' - ' . $tWeapon->Name . ' ' . $translate->get ( 'firedAtYou' ) . ' ' . $translate->get ( 'butMissed' ) . '</div>';
						break;
							
					case 'kill' :
						$retVal .= '<div>' . $translate->get ( 'youDestroyedBy' ) . $this->firingShipName . '</div>';
						break;
							
					case 'weaponDamaged' :
						$retVal .= '<div style="color: yellow;">' . $translate->get ( 'yourWeaponDamagedBy' ) . $this->firingShipName . '</div>';
						break;
							
					case 'equipmentDamaged' :
						$retVal .= '<div style="color: yellow;">' . $translate->get ( 'yourEquipmentDamagedBy' ) . $this->firingShipName . '</div>';
						break;
							
					case 'hit' :
						$retVal .= '<div>' . $this->firingShipName . ' - ' . $tWeapon->Name . ' ' . $translate->get ( 'firedAtYou' ) . ' ' . $translate->get ( 'hitDealing' );
						$retVal .= ' ' . $this->shdDamage . ' SHD,';
						$retVal .= ' ' . $this->armDamage . ' ARM,';
						$retVal .= ' ' . $this->powDamage . ' POW,';
						$retVal .= ' and ' . $this->empDamage . ' EMP ';
						$retVal .= $translate->get ( 'damage' );
						$retVal .= ' <span style="font-size: 0.65em;">(T-'.$tTimeDiff.')</span>';
						$retVal .= '</div>';
						break;

					case 'critic' :
						$retVal .= '<div style="color: red;">' . $this->firingShipName . ' - ' . $tWeapon->Name . ' ' . $translate->get ( 'firedAtYou' ) . ' ' . $translate->get ( 'hitDealing' );
						$retVal .= ' ' . $this->shdDamage . ' SHD,';
						$retVal .= ' ' . $this->armDamage . ' ARM,';
						$retVal .= ' ' . $this->powDamage . ' POW,';
						$retVal .= ' and ' . $this->empDamage . ' EMP ';
						$retVal .= $translate->get ( 'damage' );
						$retVal .= ' <span style="font-size: 0.65em;">(T-'.$tTimeDiff.')</span>';
						$retVal .= '</div>';
						break;

				}

				break;
					
			case 'player':

				/*
				 * Kontent strzelanego
				 */

				switch ($this->type) {

					case 'noPower' :
						$retVal .= '<div style="color: aqua;">' . $translate->get ( 'noPowerToFire' ) . $tWeapon->Name . '</div>';
						break;
							
					case 'noAmmo' :
						$retVal .= '<div style="color: aqua;">' . $translate->get ( 'noAmmoToFire' ) . $tWeapon->Name . '</div>';
						break;
							
					case 'empDamage' :
						$retVal .= '<div>' . $translate->get ( 'shipMalfunctionEmp' ) . '</div>';
						break;
							
					case 'miss' :
						$retVal .= '<div style="color: #FF00FF;">' . $tWeapon->Name . ' ' . $translate->get ( 'firedAt' ) . ' ' . $this->targetShipName . ' ' . $translate->get ( 'missed' ) . '</div>';
						break;
							
					case 'kill' :
						$retVal .= '<div>' . $this->targetShipName . ' ' . $translate->get ( 'isDestroyed' ) . '</div>';
						break;
							
					case 'weaponDamaged' :
						$retVal .= '<div style="color: yellow;">' . $translate->get ( 'youDamagedQeaponOf' ) . ' ' . $this->targetShipName . '</div>';
						break;
							
					case 'equipmentDamaged' :
						$retVal .= '<div style="color: yellow;">' . $translate->get ( 'youDamagedEquipmentOf' ) . ' ' . $this->targetShipName . '</div>';
						break;
							
					case 'hit' :
						$retVal .= '<div>' . $tWeapon->Name . ' ' . $translate->get ( 'firedAt' ) . ' ' . $this->targetShipName . ' ' . $translate->get ( 'hitDealing' );

						$retVal .= ' ' . $this->shdDamage . ' SHD,';
						$retVal .= ' ' . $this->armDamage . ' ARM,';
						$retVal .= ' ' . $this->powDamage . ' POW,';
						$retVal .= ' and ' . $this->empDamage . ' EMP ';
						$retVal .= $translate->get ( 'damage' );
						$retVal .= '</div>';
						break;

					case 'critic' :
						$retVal .= '<div style="color: red;">' . $tWeapon->Name . ' ' . $translate->get ( 'firedAt' ) . ' ' . $this->targetShipName . ' ' . $translate->get ( 'hitDealing' );
						$retVal .= ' ' . $this->shdDamage . ' SHD,';
						$retVal .= ' ' . $this->armDamage . ' ARM,';
						$retVal .= ' ' . $this->powDamage . ' POW,';
						$retVal .= ' and ' . $this->empDamage . ' EMP ';
						$retVal .= $translate->get ( 'damage' );
						$retVal .= '</div>';
						break;


				}

				break;

		}
		return $retVal;
	}

	/**
	 * Czy są jakies combat messages
	 *
	 * @param int $userID
	 * @return boolean
	 */
	static public function sCheckMessages($userID) {

		$tQuery = "SELECT COUNT(*) AS ILE FROM combatmessages WHERE UserID='{$userID}' AND Displayed='no' AND Type='defensive'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		if (\Database\Controller::getInstance()->fetch ( $tQuery )->ILE == 0) {
			return false;
		} else {
			return true;
		}
	}

}
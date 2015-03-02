<?php

namespace Controlpanel;

class Controller {

	private function __construct() {

	}

	static public function mainPage() {

		$retVal = '';
		$listeners = '';
		
		//FIXME refactor
		$listeners .= \user::sLogoutListener ( $_REQUEST );
		$listeners .= \user::sLoginListener ( $_REQUEST );
		
		if (empty ( $_SESSION ['cpLoggedUserID'] )) {
		
			/*
			 * User nie zalogowany
			*/
			$template = new \General\Templater ( 'templates/loginPage.html' );
			$template->add ( 'listeners', $listeners );
			$retVal .= $template;
		} else {
		
			$user = new \user ( $_SESSION ['cpLoggedUserID'] );
		
			$indexTemplate = new \General\Templater ( 'templates/mainPage.html' );
		
			\Listener\Message::getInstance()->register($_REQUEST, $indexTemplate);
			
			$mainText = '';
		
			if (class_exists ( $_REQUEST ['class'] )) {
		
				try {
		
					$tClass = $_REQUEST ['class'];
					$tObject = new $tClass ( );
		
					if (method_exists ( $tObject, $_REQUEST ['method'] )) {
		
						$tMethod = $_REQUEST ['method'];
		
						$mainText .= $tObject->{$tMethod} ( $user, $_REQUEST );
					} else {
						throw new \customException ( 'Unknown plugin' );
					}
		
					unset ( $tObject );
		
				} catch ( \customException $e ) {
					/**
					 * Przechwycenie customException w celu przekazania komunikatu
					 */
					$mainText .= \psDebug::displayBox ( $e->getMessage (), array ('attach' => false ) );
				} catch ( \Exception $e ) {
					/*
					 * Zwykły Exception
					*/
					$mainText .= \psDebug::cThrow ( null, $e );
				}
			} else {
				$mainText .= \psDebug::displayBox ( 'Unknown plugin', array ('attach' => false ) );
			}
		
			$indexTemplate->add ( 'content', $mainText );
		
			$indexTemplate->add ( 'leftMenu', Menu::get() );
		
			\Listener\LowLevelMessage::getInstance()->register($_REQUEST, $indexTemplate);
			
			$indexTemplate->add('listeners','');
			
			$retVal .= $indexTemplate;
		
		}
		
		echo $retVal;
		//@todo JS errorDialog przepisać na obiekty

	}

}
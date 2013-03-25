<?php
namespace Portal;

class Controller {

	private function __construct() {

	}
 
	static public function mainPage() {

		global $deploymentMode, $config, $portalNews2Page;

		if (!empty($deploymentMode) && mb_strpos ( $_SERVER ['HTTP_HOST'], 'en.' ) === false && mb_strpos ( $_SERVER ['HTTP_HOST'], 'pl.' ) === false) {
			if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) && mb_strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'pl-PL') !== false) {
				header("Location: http://pl.{$config ['general'] ['url']}",TRUE,301);
			}else {
				header("Location: http://en.{$config ['general'] ['url']}",TRUE,301);
			}
		}

		if (empty ( $_SESSION ['language'] )) {
			$_SESSION ['language'] = 'en';
		}

		if (mb_strpos ( $_SERVER ['HTTP_HOST'], 'pl.' ) !== false) {
			$_SESSION ['language'] = 'pl';
		}

		if (mb_strpos ( $_SERVER ['HTTP_HOST'], 'en.' ) !== false) {
			$_SESSION ['language'] = 'en';
		}

		$language = $_SESSION ['language'];
		$_REQUEST['language'] = $language;

		\TranslateController::setDefaultLanguage($language);
		$t = \TranslateController::getDefault();

		$template = new \General\Templater ( 'templates/mainPage.html' );

		$template->add ( 'gameUrl', $config ['general'] ['url'] );

		$mainContent = '';

		try {

			if (empty ( $_REQUEST ['class'] )) {
				$_REQUEST ['class'] = null;
			}

			if (empty ( $_REQUEST ['method'] )) {
				$_REQUEST ['method'] = null;
			}

			/**
			 * Wywołanie odpowiednich pluginów
			 */
			if (! empty ( $_REQUEST ['class'] ) && ! empty ( $_REQUEST ['method'] )) {

				if (class_exists ( $_REQUEST ['class'] )) {

					$tClass = $_REQUEST ['class'];
					$tObject = new $tClass ( );

					if (method_exists ( $tObject, $_REQUEST ['method'] )) {
						$tMethod = $_REQUEST ['method'];
						$mainContent .= $tObject->{$tMethod} ( $_REQUEST, $template );
					} else {
						throw new \customException ( 'Unknown function' );
					}

				} else {
					throw new \customException ( 'Unknown plugin' );
				}

			}
			else {
				$registry = new NewsRegistry( $language, $portalNews2Page );
				$mainContent .= $registry->get ();
				unset($registry);
			}

		} catch ( \customException $e ) {
			$mainContent .= \psDebug::cThrow ( $e->getMessage (), $e, array ('write' => false, 'send' => false, 'display' => false ) );
		} catch ( \Exception $e ) {
			\psDebug::halt ( null, $e );
		}

		/*
		 * Okno logowania
		*/
		if (! $config ['general'] ['enableLogin'] && empty($_SESSION['cpLoggedUserID'])) {
			$template->remove ( 'loginForm' );
			$template->remove ( 'fbLogin' );
			$template->add ( 'loginText', '<p>{T:loginDisabled}</p>' );
		} else {
			$template->add ( 'loginText', '' );
		}

		if (empty($config ['general'] ['enableRegister']) && empty($_SESSION['cpLoggedUserID'])) {
			$template->remove('registerLink');
		}

		$template->add ( 'languageVar', $language );
		$template->add ( 'mainContent', $mainContent );

		$registry = new \portalMainNews ( $language );
		$template->add('mottoText', $registry->render ());
		unset($registry);

		$registry = new Chat ();
		$template->add('gameplayChat', $registry->render ());
		unset($registry);

		$template->add('menuNavigator', Menu::render($_REQUEST) );

		$template->add('pageTitle',$config ['general'] ['pageTitle']);

		/*
		 * Attach JS and CSS scripts
		*/
		$template->add('scripts',\General\Minify::getInstance()->getScript('portal_js'));
		$template->add('css',\General\Minify::getInstance()->getCss('portal_css'));

		/*
		 * google analytics
		*/
		if (!empty($config['analytics']['id'])) {
			$template->add('analyticsID',$config['analytics']['id']);
			$template->add('analyticsDomain',$config['analytics']['domain']);
		}
		else {
			$template->remove('googleAnalytics');
		}

		$template->add('cdnUrl',$config['general']['cdn']);
		$template->add('appId',$config['facebook']['appId']);

		echo $template;

	}

}
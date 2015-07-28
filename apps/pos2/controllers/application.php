<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

Core::importFromActiveApp('library/POSGardien/POSGardien.php');
Core::importFromLibrary('Hfos', 'Loader/Loader.php');

/**
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los metodos aquí definidos estan disponibles para
 * cualquier controlador.
 *
 * @category 	Kumbia
 * @package 	Controller
 **/
class ControllerBase
{

	/**
	 * Nombre de la aplicación
	 *
	 */
	const APP_NAME = 'Punto de Venta';

	/**
	 * Nombre de Código
	 *
	 */
	const APP_CODE_NAME = 'Bodhi';

	/**
	 * Versión de la aplicación
	 *
	 */
	const APP_VERSION = '6.0.3';

	/**
	 * Revisión
	 *
	 */
	const APP_REVISION = '$Revision$ $Date$';

	/**
	 * Último commit realizado
	 *
	 */
	const APP_LAST_COMMIT = '$Last-Commit: Tue, 07 Feb 2012 15:03:04 -0500$';

	/**
	 * Versiones compatibles con la actualización
	 *
	 * @var array
	 */
	static $compatibleVersions = array(
		'5.4', '5.4.1', '5.4.2', '5.4.3', '5.4.4',
		'5.4.5', '5.4.6', '5.4.7', '5.4.8', '5.4.9',
		'5.4.10', '5.4.11', '5.4.12', '5.4.13', '5.4.14',
		'6.0.0', '6.0.1', '6.0.2', '6.0.3'
	);

	/**
	 * Este método es llamado al iniciar la aplicación
	 *
	 */
	public function init()
	{
		if (Browser::isMobile() == false) {
			if (Browser::isFirefox()) {
				if (version_compare(Browser::getVersion(), '4.0.0', '<')) {
					Router::routeTo(array('controller' => 'firefox'));
				} else {
					Router::routeTo(array('controller' => 'appmenu'));
				}
			} else {
				Router::routeTo(array('controller' => 'appmenu'));
			}
			self::checkVersion();
		} else {
			Router::routeTo(array('controller' => 'mobile'));
		}
	}

	/**
	 * beforeFilter
	 *
	 * @return boolean
	 */
	public function beforeFilter()
	{
		if (POSGardien::isAllowed()) {
			i18n::isUnicodeEnabled();
			LocaleMath::enableBcMath();
		} else {
			return false;
		}
	}

	/**
	 * Revisa si tiene la última versión
	 *
	 * @return boolean
	 */
	public static function checkVersion()
	{
		if (Session::get('versionCheck') == false) {
			$Datos = EntityManager::getEntityInstance('Datos');
			if ($Datos->hasField('version') == false) {
				Flash::notice('Versión anterior a 5.2');
				Router::routeTo('controller: upgrade', 'action: index', 'id: 54');
			} else {
				$datos = $Datos->findFirst('columns: version');
				$version = $datos->getVersion();
				if ($version == '' || version_compare($version, ControllerBase::APP_VERSION, '<')) {
					foreach(self::$compatibleVersions as $compatibleVersion){
						if(version_compare($version, $compatibleVersion, '<')){
							Flash::notice('Versión anterior a ' . $compatibleVersion);
							return Router::routeTo(array(
								'controller' => 'upgrade',
								'action' => 'index',
								'id' => $compatibleVersion
							));
						}
					}
				}
				Session::set('versionCheck', true);
			}
		}
	}

	public function getViewExceptionHandler()
	{
		if (Router::isInitialized() == true) {
			$controllerRequest = ControllerRequest::getInstance();
			$config = CoreConfig::readAppConfig();
			if ($config->application->mode == 'production' && PHP_OS != 'Darwin') {
				return array('Socorro', 'exceptionHandler');
			} else {
				return array('View', 'handleViewExceptions');
			}
		} else {
			return array('View', 'handleViewExceptions');
		}
	}

	public function notFoundAction()
	{
		Router::routeTo(array('controller' => 'firefox', 'action' => 'notFound'));
	}

}

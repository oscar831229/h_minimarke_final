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

/**
 * AdminController
 *
 * Controlador que administra el ingreso a la sección administrativa
 *
 */
class AdminController extends ApplicationController
{

	public function beforeFilter()
	{
		if (POSGardien::hasPanic()==true) {
			Router::routeTo(array('controller' => 'panic'));
			return false;
		}
		parent::beforeFilter();
	}

	public function indexAction()
	{

	}

	public function loginAction()
	{
		$pass = $this->getPostParam('pass', 'alpha');
		$usuario = $this->UsuariosPos->findFirst("clave='$pass' AND estado='A'");
		if ($usuario) {
			if ($usuario->perfil == 'Administradores' || $usuario->perfil == 'JefeDeAyB') {
				Flash::success('Bienvenido ' . $usuario->nombre);
				Session::set('auth', 'admin');
				Session::set('usuarioId', $usuario->id);
				Session::set('role', $usuario->perfil);
				POSGardien::successAccess();
				$this->routeTo('controller: menus');
			} else {
				POSGardien::failedAccess();
				Flash::error('Permisos Insuficientes/Password Incorrecto');
				Tag::displayTo('pass', '');
				$this->routeTo('action: index');
			}
		} else {
			Flash::error('Permisos Insuficientes/Password Incorrecto');
			Session::unsetData('auth');
			$controllerRequest = ControllerRequest::getInstance();
			$controllerRequest->unsetPostParam('pass');
			Tag::displayTo('pass', '');
			$this->routeTo('action: index');
			$numberFailed = Session::get('numberFailed');
			if ($numberFailed > 2) {
				sleep(2);
			}
			$numberFailed++;
			Session::set('numberFailed', $numberFailed);
		}
	}

	public function logoutAction()
	{
		GarbageCollector::freeControllerData('menus');
		GarbageCollector::freeControllerData('menus_items');
		GarbageCollector::freeControllerData('salon');
		GarbageCollector::freeControllerData('salon_menus_items');
		Session::unsetData('auth');
		$this->routeTo('controller: appmenu');
	}

}

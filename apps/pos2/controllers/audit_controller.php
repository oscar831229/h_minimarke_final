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

class AuditController extends ApplicationController
{

	public $audits;
	public $fecha;

	public function indexAction()
	{
		$usuario = $this->UsuariosPos->findFirst(Session::get("usuarios_id"));
		if ($usuario->perfil=="Administradores") {
			$controllerRequest = ControllerRequest::getInstance();
			if ($controllerRequest->isSetPostParam("fecha")==true) {
				$fecha = $controllerRequest->getParamPost("fecha", "date");
				$this->audits = $this->Audit->find("date(fecha_at)='{$fecha}'", "order: fecha_at DESC");
				if(count($this->audits)==0){
					Flash::notice('No hay registros de auditoría en la fecha indicada');
				}
			} else {
				$this->audits = $this->Audit->find("order: fecha_at DESC", "limit: 50");
				if(count($this->audits)){
					Flash::notice('Se están visualizando los últimos 50 registros');
				} else {
					Flash::notice('No hay registros de auditoría');
				}
			}
			$this->fecha = Date::getCurrentDate();
		} else {
			Flash::error('Debe ser un administrador del sistema para usar este modulo');
			$this->routeTo(array('controller' => 'appmenu'));
		}
	}

}
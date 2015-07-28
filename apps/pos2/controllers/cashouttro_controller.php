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

class CashouttroController extends ApplicationController
{

	public function indexAction()
	{
		$this->loadModel('CashTray', 'UsuariosPos');
	}

	public function openAction($id)
	{
		$id = $this->filter($id, 'int');
		if ($this->CashTray->findFirst($id)) {
			$conditions = "usuarios_id = '".Session::getData("usuarios_id")."' and estado = 'A'";
			if($this->CashTray->count($conditions)){
				Flash::notice("El usuario ya tiene una caja abierta");
				return $this->routeTo(array('action' => 'index'));
			}
			$this->Datos->findFirst();
			$this->CashTray->usuarios_id = Session::getData("usuarios_id");
			$this->CashTray->fecha = (string)$this->Datos->getFecha();
			$this->CashTray->hora_abierta = Date::getCurrentTime();
			$this->CashTray->estado = 'A';
			if(!$this->CashTray->save()){
				foreach($this->CashTray->getMessages() as $message){
					Flash::error($message->getMessage());
				}
			}
		}
		return $this->routeTo(array('action' => 'index'));
	}

	public function closeAction($id)
	{
		$id = $this->filter($id, 'int');
		if ($this->CashTray->findFirst($id)) {
			$this->Datos->findFirst();
			$this->CashTray->usuarios_id = Session::getData("usuarios_id");
			$this->CashTray->fecha = (string)$this->Datos->getFecha();
			$this->CashTray->hora_cerrada = Date::getCurrentTime();
			$this->CashTray->estado = 'N';
			if(!$this->CashTray->save()){
				foreach($this->CashTray->getMessages() as $message){
					Flash::error($message->getMessage());
				}
			}
		}
		return $this->routeTo(array('action' => 'index'));
	}

}

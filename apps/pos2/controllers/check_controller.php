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

class CheckController extends ApplicationController
{

	public function indexAction()
	{
		$this->loadModel('Account', 'AccountMaster', 'MenusItems');
		$this->setParamToView('salonMesas', $this->SalonMesas->find("estado='A'"));
		$this->setParamToView('screens', $this->Screens->find("estado='A'"));
	}

	public function getContentAction()
	{
		$this->setResponse('view');

		$screenId = $this->getPost('screenId', 'int');
		$query = new ActiveRecordJoin(array(
			'entities' => array(
				'MenusItems', 'SalonMenusItems', 'Account', 'AccountMaster',
				'SalonMesas', 'Salon'
			),
			'fields' => array(
				'id' => '{#Account}.id',
				'nombre_pedido' => '{#MenusItems}.nombre_pedido',
				'cantidad' => '{#Account}.cantidad',
				'tiempo' => 'timediff(current_time, {#Account}.tiempo)',
				'ambiente' => '{#Salon}.nombre',
				'mesero' => '{#AccountMaster}.nombre',
				'nota' => '{#Account}.note',
			),
			'conditions' =>
				"{#SalonMenusItems}.screens_id = '".$screenId."' AND
				{#Account}.send_kitchen = 'S' AND
				{#Account}.estado = 'S' ",
			'order' => array('tiempo DESC')
		));
		$this->setParamToView('accounts', $query->getResultSet());
		$this->loadModel('AccountModifiers');
	}

	public function statusAction($id = null)
	{
		$id = $this->filter($id, 'int');
		$account = $this->Account->findFirst($id);
		if ($account != false) {
			$account->estado = 'A';
			$account->tiempo_final = date("H:i:s");
			$account->cantidad_atendida = $account->cantidad;
			if ($account->save() == false) {
				foreach ($account->getMessage() as $message) {
					Flash::error($message->getMessage());
				}
			}
		}
	}

}

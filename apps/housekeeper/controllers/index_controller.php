<?php

class IndexController extends ApplicationController {

	public function indexAction(){
		$query = new ActiveRecordJoin(array(
			'entities' => array('Usuarios', 'Camarera'),
			'conditions' => "",
			'fields' => array(
				'{#Usuarios}.nombre',
				'{#Usuarios}.login',
			),
			'order' => array('{#Usuarios}.nombre')
		));
		$usuarios = array();
		foreach($query->getResultSet() as $item){
			$usuarios[$item->login] = $item->nombre;
		}
		$this->setParamToView('usuarios', $usuarios);
	}

}
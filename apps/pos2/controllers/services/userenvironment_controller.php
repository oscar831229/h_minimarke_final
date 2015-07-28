<?php

class UserEnvironmentController extends WebServiceController {

	/**
	 * Devuelve los ambientes en los que el usuario esta autorizado a trabajar
	 *
	 * @return array
	 */
	public function getAmbientesAction(){
		$userIdentity = $this->getService('Services.UserIdentity');
		$userId = $userIdentity->getUserId();
		$ambientes = array();
		$query = new ActiveRecordJoin(array(
			'fields' => array('{#Salon}.id', '{#Salon}.nombre'),
			'entities' => array('Usuarios', 'Permisos', 'Salon'),
			'conditions' => "{#Usuarios}.usuarios_id = '{$userId}' AND {#Permisos}.estado = 'A'"
		));
		foreach($query->getResultSet() as $row){
			$ambientes[$row->getId()][] = $row->getNombre();
		}
		return $ambientes;
	}

}
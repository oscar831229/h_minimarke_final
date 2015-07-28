<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class POSAudit extends AuditLogger {

	public function __construct($note, $transaction=null){
		parent::__construct('Audit');
		$this->bindToField('USER_ID', 'usuarios_id');
		$this->bindToField('USERNAME', 'nombre');
		$this->bindToField('NOTE', 'nota');
		$this->bindToField('IP_ADDRESS', 'ipaddress');
		$this->setFieldData('controller', Router::getController());
		$this->setFieldData('action', Router::getAction());
		if(Session::isSetData('usuarioId')){
			$this->setFieldData('USER_ID', Session::get('usuarioId'));
			$this->setFieldData('USERNAME', Session::get('auth'));
		} else {
			$this->setFieldData('USER_ID', Session::get('usuarios_id'));
			$this->setFieldData('USERNAME', Session::get('usuarios_nombre'));
		}
		$this->setFieldData('NOTE', $note);
		if($transaction!=null){
			$this->setTransaction($transaction);
		}
		$this->commit();
	}

}
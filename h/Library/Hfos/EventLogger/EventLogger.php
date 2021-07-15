<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * EventLogger
 *
 * Este componente permite auditar eventos
 *
 */
class EventLogger extends AuditLogger {

	/**
	 * Constructor de EventLogger
	 *
	 * @param string $note
	 * @param string $type
	 * @param ActiveRecordTransaction $transaction
	 */
	public function __construct($note, $type, $transaction=null){
		$identity = IdentityManager::getActive();
		if($identity['id']>0){
			$config = CoreConfig::readEnviroment();
			parent::__construct('Auditlog');
			$this->bindToField('USER_ID', 'codusu');
			$this->bindToField('NOTE', 'nota');
			$this->bindToField('IP_ADDRESS', 'ip');
			$this->setFieldData('USER_ID', $identity['id']);
			$this->setFieldData('NOTE', $note);
			$this->setFieldData('db', $config->database->name);
			$this->setFieldData('modulo', Router::getController());
			$this->setFieldData('fecha', Date::getCurrentDate());
			$this->setFieldData('fecsis', Date::getCurrentDate());
			$this->setFieldData('hora', Date::getCurrentTime());
			$this->setFieldData('tipo', $type);
			if($transaction!=null){
				$this->setTransaction($transaction);
			}
			$this->commit();
		} else {
			throw new AuditLoggerException('El perfil público no está habilitado para registrar eventos de auditoría');
		}
	}

}
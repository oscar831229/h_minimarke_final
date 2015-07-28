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

/**
 * POSRcs
 *
 */
class POSRcs extends UserComponent
{

	/**
	 * Indica si el RCS está deshabilitado
	 *
	 * @var boolean
	 */
	private static $_disabled = false;

	/**
	 * Campos que cambiaron al actualizar un registro
	 *
	 * @var array
	 */
	private static $_rcsChanged = array();

	/**
	 * Modelo original sin modificar
	 *
	 * @var ActiveRecordBase
	 */
	private static $_originalRecord = null;

	/**
	 * Evento para grabar el maestro de la revisión
	 *
	 * @param ActiveRecordBase $record
	 */
	private static function _createRevision($record)
	{
		$controllerRequest = ControllerRequest::getInstance();
		$rcsRevision = new Revisions();
		$rcsRevision->setConnection($record->getConnection());
		$schema = $record->getSchema();
		if($schema==''){
			$rcsRevision->setDb($record->getConnection()->getDatabaseName());
		} else {
			$rcsRevision->setDb($schema);
		}
		$rcsRevision->setSource($record->getSource());
		$rcsRevision->setCodusu(Session::get('usuarioId'));
		$rcsRevision->setIpaddress($controllerRequest->getClientAddress());
		$rcsRevision->setFecha(time());
		if($rcsRevision->save()==false){
			foreach($rcsRevision->getMessages() as $message){
				Flash::error($message->getMessage());
			}
			return false;
		}
		return $rcsRevision;
	}

	/**
	 * Evento para preparar la revisión antes de insertar un registro
	 *
	 * @param ActiveRecordBase $record
	 */
	public static function beforeCreate()
	{
		self::$_rcsChanged = array();
		return true;
	}

	/**
	 * Evento para preparar la revisión antes de actualizar un registro
	 *
	 * @param ActiveRecordBase $record
	 */
	public static function beforeUpdate($record)
	{
		if(self::$_disabled==false){
			self::$_rcsChanged = array();
			self::$_originalRecord = EntityManager::getEntityInstance(get_class($record))->findFirst($record->readAttribute('id'));
			if(self::$_originalRecord==false){
				throw new CoreException('No se pudo auditar el registro '.$record->readAttribute('id'));
			} else {
				foreach($record->getAttributes() as $attribute){
					if($record->readAttribute($attribute)!=self::$_originalRecord->readAttribute($attribute)){
						self::$_rcsChanged[$attribute] = true;
					}
				}
			}
		}
		return true;
	}

	/**
	 * Evento para grabar la revisión después de actualizar un registro
	 *
	 * @param ActiveRecordBase $record
	 */
	public static function afterUpdate($record)
	{
		if (self::$_disabled == false) {
			$rcsRevision = self::_createRevision($record);
			if ($rcsRevision == false) {
				return false;
			}
			foreach ($record->getAttributes() as $attribute) {
				$rcsRecord = new Records();
				$rcsRecord->setConnection($rcsRevision->getConnection());
				$rcsRecord->setRevisionsId($rcsRevision->getId());
				$rcsRecord->setFieldName($attribute);
				$rcsRecord->setValue($record->readAttribute($attribute));
				if (isset(self::$_rcsChanged[$attribute])) {
					$rcsRecord->setChanged('S');
				} else {
					$rcsRecord->setChanged('N');
				}
				if ($attribute=='id') {
					$rcsRecord->setIsPrimary('S');
				} else {
					$rcsRecord->setIsPrimary('N');
				}
				if ($rcsRecord->save()==false) {
					foreach ($rcsRecord->getMessages() as $message) {
						Flash::error($message->getMessage());
					}
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Evento para grabar la revisión después de insertar un registro
	 *
	 * @param ActiveRecordBase $record
	 */
	public static function afterCreate($record)
	{
		if (self::$_disabled==false) {
			$rcsRevision = self::_createRevision($record);
			if ($rcsRevision==false) {
				return false;
			}
			foreach ($record->getAttributes() as $attribute) {
				$rcsRecord = new Records();
				$rcsRecord->setConnection($rcsRevision->getConnection());
				$rcsRecord->setRevisionsId($rcsRevision->getId());
				$rcsRecord->setFieldName($attribute);
				$rcsRecord->setValue($record->readAttribute($attribute));
				$rcsRecord->setChanged('N');
				if ($attribute=='id'){
					$rcsRecord->setIsPrimary('S');
				} else {
					$rcsRecord->setIsPrimary('N');
				}
				if ($rcsRecord->save()==false) {
					foreach($rcsRecord->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					return false;
				}
			}
			return true;
		}
	}

	/**
	 * Deshabilita el RCS
	 *
	 */
	public static function disable()
	{
		self::$_disabled = true;
	}

}
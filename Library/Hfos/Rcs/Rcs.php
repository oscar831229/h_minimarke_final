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
 * Rcs
 *
 * Este componente permite generar revisiones sobre cambios en los registros
 *
 */
class Rcs extends UserComponent
{

	/**
	 * RevisionBase
	 *
	 * @var Revisions
	 */
	private static $_revisionBase = null;

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
	 * Llave primaria del objeto activo
	 *
	 * @var array
	 */
	private static $_primaryKeys = array();

	/**
	 * Checksum de la llave primaria del registro
	 *
	 * @var string
	 */
	private static $_checksum = '';

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
		if (self::$_revisionBase===null) {
			$identity = IdentityManager::getActive();
			$controllerRequest = ControllerRequest::getInstance();
			self::$_revisionBase = EntityManager::getEntityInstance('Revisions');
			self::$_revisionBase->setCodusu($identity['id']);
			$ipAddress = $controllerRequest->getClientAddress();
			if($ipAddress==''){
				$ipAddress = '127.0.0.1';
			}
			self::$_revisionBase->setIpaddress($ipAddress);
		}
		$rcsRevision = clone self::$_revisionBase;
		$rcsRevision->setConnection($record->getConnection());
		$schema = $record->getSchema();
		if($schema==''){
			$schema = $record->getConnection()->getDatabaseName();
		}
		$rcsRevision->setDb($schema);
		$rcsRevision->setSource($record->getSource());
		$rcsRevision->setFecha(time());
		$rcsRevision->setChecksum(md5($schema . $record->getSource() . self::$_checksum));
		if($rcsRevision->save()==false){
			foreach($rcsRevision->getMessages() as $message){
				Flash::error($message->getMessage());
			}
			return false;
		}
		return $rcsRevision;
	}

	/**
	 *
	 */
	public static function getOrCreatePartition($connection, $source)
	{

		/*$recordsSource = "records_" . substr($source, 0, 3);

		$sql = "SELECT IF(COUNT(*)>0, 1, 0) FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_NAME`='" . $recordsSource . "' AND `TABLE_SCHEMA`='" . RCS_DB . "'";
		$table = $db->fetchOne($sql);
		if (!$table[0]) {
			$sql = "CREATE TABLE " . RCS_DB . ".`$recordsSource` (
  				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  				`revisions_id` int(10) unsigned NOT NULL,
  				`field_name` varchar(24) NOT NULL,
  				`value` varchar(255) DEFAULT NULL,
  				`is_primary` char(1) NOT NULL,
  				`changed` char(1) NOT NULL,
  				PRIMARY KEY (`id`),
  				KEY `revisions_id_name` (`revisions_id`,`field_name`,`value`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8";
			$db->query($sql);
		}*/

		//return $recordsSource;
	}

	/**
	 * Evento para preparar la revisión antes de insertar un registro
	 *
	 * @param ActiveRecordBase $record
	 * @return boolean
	 */
	public static function beforeCreate($record)
	{
		self::$_rcsChanged = array();
		self::$_primaryKeys = array();
		$checksum = '';
		foreach ($record->getPrimaryKeyAttributes() as $attribute) {
			$checksum .= $record->readAttribute($attribute);
			self::$_primaryKeys[$attribute] = true;
		}
		self::$_checksum = $checksum;
		return true;
	}

	/**
	 * Evento para preparar la revisión antes de actualizar un registro
	 *
	 * @param ActiveRecordBase $record
	 * @return boolean
	 */
	public static function beforeUpdate($record)
	{
		if (self::$_disabled == false) {
			self::$_rcsChanged = array();
			self::$_primaryKeys = array();
			$checksum = '';
			$conditions = array();
			foreach ($record->getPrimaryKeyAttributes() as $attribute) {
				$conditions[] = $attribute.'=\''.$record->readAttribute($attribute).'\'';
				$checksum.=$record->readAttribute($attribute);
				self::$_primaryKeys[$attribute] = true;
			}
			self::$_checksum = $checksum;
			$entityInstance = EntityManager::getEntityInstance(get_class($record));
			$hasTransaction = TransactionManager::hasUserTransaction();
			if ($hasTransaction) {
				$transaction = TransactionManager::getUserTransaction();
				$entityInstance->setTransaction($transaction);
			}
			self::$_originalRecord = $entityInstance->findFirst(join(' AND ', $conditions));
			if (self::$_originalRecord == false) {
				throw new CoreException(get_class($record).': No se pudo auditar el registro '.join(' AND ', $conditions));
			} else {
				foreach ($record->getAttributes() as $attribute) {
					if ($record->readAttribute($attribute)!=self::$_originalRecord->readAttribute($attribute)){
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
		$disableEvents = ActiveRecord::getDisableEvents();
		$refreshPersistance = ActiveRecord::getRefreshPersistance();
		if (self::$_disabled == false) {

			ActiveRecord::disableEvents(true);
			ActiveRecord::refreshPersistance(false);
			$rcsRevision = self::_createRevision($record);
			if ($rcsRevision == false) {
				return false;
			}

			//$recordsSource = self::getOrCreatePartition($rcsRevision->getConnection(), $rcsRevision->getSource());

			$recordsEntity = EntityManager::getEntityInstance('Records');
			$recordsEntity->setConnection($rcsRevision->getConnection());
			$recordsEntity->setRevisionsId($rcsRevision->getId());
			//$recordsEntity->setSource($recordsSource);

			foreach ($record->getAttributes() as $attribute) {
				$rcsRecord = clone $recordsEntity;
				$rcsRecord->setFieldName($attribute);
				$rcsRecord->setValue($record->readAttribute($attribute));
				if (isset(self::$_rcsChanged[$attribute])){
					$rcsRecord->setChanged('S');
				} else {
					$rcsRecord->setChanged('N');
				}
				if (isset(self::$_primaryKeys[$attribute])){
					$rcsRecord->setIsPrimary('S');
				} else {
					$rcsRecord->setIsPrimary('N');
				}
				if ($rcsRecord->save()==false){
					foreach($rcsRecord->getMessages() as $message){
						$this->appendMessage(new ActiveRecordMessage('RCS: '.$message->getMessage(), ''));
					}
					return false;
				}
			}
			unset($rcsRevision);
		}
		ActiveRecord::disableEvents($disableEvents);
		ActiveRecord::refreshPersistance($refreshPersistance);
		return true;
	}

	/**
	 * Evento para grabar la revisión después de insertar un registro
	 *
	 * @param ActiveRecordBase $record
	 */
	public static function afterCreate($record)
	{
		$disableEvents = ActiveRecord::getDisableEvents();
		$refreshPersistance = ActiveRecord::getRefreshPersistance();
		if(self::$_disabled==false){
			ActiveRecord::disableEvents(true);
			ActiveRecord::refreshPersistance(false);
			$rcsRevision = self::_createRevision($record);
			if($rcsRevision==false){
				return false;
			}
			$recordsEntity = EntityManager::getEntityInstance('Records');
			$recordsEntity->setConnection($rcsRevision->getConnection());
			$recordsEntity->setRevisionsId($rcsRevision->getId());
			foreach ($record->getAttributes() as $attribute) {
				$rcsRecord = clone $recordsEntity;
				$rcsRecord->setConnection($rcsRevision->getConnection());
				$rcsRecord->setRevisionsId($rcsRevision->getId());
				$rcsRecord->setFieldName($attribute);
				$rcsRecord->setValue($record->readAttribute($attribute));
				$rcsRecord->setChanged('N');
				if(isset($primaryKeys[$attribute])){
					$rcsRecord->setIsPrimary('S');
				} else {
					$rcsRecord->setIsPrimary('N');
				}
				if($rcsRecord->save()==false){
					foreach($rcsRecord->getMessages() as $message){
						$this->appendMessage(new ActiveRecordMessage('RCS: '.$message->getMessage(), ''));
					}
					return false;
				}
			}
			unset($rcsRevision);
		}
		ActiveRecord::disableEvents($disableEvents);
		ActiveRecord::refreshPersistance($refreshPersistance);
		return true;
	}

	/**
	 * Evento para preparar la revisión antes de borrar un registro
	 *
	 * @param ActiveRecordBase $record
	 * @return boolean
	 */
	public static function beforeDelete($record)
	{
		$disableEvents = ActiveRecord::getDisableEvents();
		$refreshPersistance = ActiveRecord::getRefreshPersistance();
		if(self::$_disabled==false){
			ActiveRecord::disableEvents(true);
			ActiveRecord::refreshPersistance(false);
			$rcsRevision = self::_createRevision($record);
			if($rcsRevision==false){
				return false;
			}
			$recordsEntity = EntityManager::getEntityInstance('Records');
			$recordsEntity->setConnection($rcsRevision->getConnection());
			$recordsEntity->setRevisionsId($rcsRevision->getId());
			foreach ($record->getAttributes() as $attribute) {
				$rcsRecord = clone $recordsEntity;
				$rcsRecord->setConnection($rcsRevision->getConnection());
				$rcsRecord->setRevisionsId($rcsRevision->getId());
				$rcsRecord->setFieldName($attribute);
				$rcsRecord->setValue($record->readAttribute($attribute) . " (DELETED)");
				$rcsRecord->setChanged('N');
				if(isset($primaryKeys[$attribute])){
					$rcsRecord->setIsPrimary('S');
				} else {
					$rcsRecord->setIsPrimary('N');
				}
				if($rcsRecord->save()==false){
					foreach($rcsRecord->getMessages() as $message){
						$this->appendMessage(new ActiveRecordMessage('RCS: '.$message->getMessage(), ''));
					}
					return false;
				}
			}
			unset($rcsRevision);
		}
		ActiveRecord::disableEvents($disableEvents);
		ActiveRecord::refreshPersistance($refreshPersistance);
		return true;
	}

	/**
	 * Deshabilita el RCS
	 *
	 */
	public static function disable()
	{
		self::$_disabled = true;
	}

	/**
	 * Habilita el RCS
	 *
	 */
	public static function enable()
	{
		self::$_disabled = false;
	}

}

<?php 

class EmpresaMigration_105 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('empresa', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'nit'
				)),
				new DbColumn('ciudades_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'nombre'
				)),
				new DbColumn('direccion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'ciudades_id'
				)),
				new DbColumn('telefono', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'direccion'
				)),
				new DbColumn('fax', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'telefono'
				)),
				new DbColumn('sitweb', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'fax'
				)),
				new DbColumn('email', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 120,
					'after' => 'sitweb'
				)),
				new DbColumn('serial', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'email'
				)),
				new DbColumn('version', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 7,
					'after' => 'serial'
				)),
				new DbColumn('creservas', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'notNull' => true,
					'after' => 'version'
				)),
				new DbColumn('crc', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'notNull' => true,
					'after' => 'creservas'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('creservas', array(
					'creservas'
				)),
				new DbIndex('crc', array(
					'crc'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '4',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
<?php 

class PermisoMigration_105 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('permiso', array(
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
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 100,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('controller', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 50,
					'notNull' => true,
					'after' => 'nombre'
				)),
				new DbColumn('action', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 50,
					'notNull' => true,
					'after' => 'controller'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '188',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
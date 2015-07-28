<?php 

class CriteriosProveedoresMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('criterios_proveedores', array(
			'columns' => array(
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'after' => 'comprob'
				)),
				new DbColumn('almacen', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'after' => 'numero'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'primary' => true,
					'notNull' => true,
					'after' => 'almacen'
				)),
				new DbColumn('criterios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'after' => 'nit'
				)),
				new DbColumn('puntaje', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 5,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'criterios_id'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'comprob',
					'numero',
					'almacen',
					'nit',
					'criterios_id'
				)),
				new DbIndex('nit', array(
					'nit',
					'criterios_id'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
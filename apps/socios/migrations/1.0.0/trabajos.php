<?php 

class TrabajosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('trabajos', array(
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
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('profesiones_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'socios_id'
				)),
				new DbColumn('especializaciones_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'profesiones_id'
				)),
				new DbColumn('empresa1', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 60,
					'after' => 'especializaciones_id'
				)),
				new DbColumn('cargo1', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'empresa1'
				)),
				new DbColumn('direccion1', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 80,
					'after' => 'cargo1'
				)),
				new DbColumn('telefono1', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 50,
					'after' => 'direccion1'
				)),
				new DbColumn('fax1', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 50,
					'after' => 'telefono1'
				)),
				new DbColumn('empresa2', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 60,
					'after' => 'fax1'
				)),
				new DbColumn('cargo2', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'empresa2'
				)),
				new DbColumn('direccion2', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 80,
					'after' => 'cargo2'
				)),
				new DbColumn('telefono2', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 50,
					'after' => 'direccion2'
				)),
				new DbColumn('fax2', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 50,
					'after' => 'telefono2'
				)),
				new DbColumn('comentario', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'fax2'
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
				'AUTO_INCREMENT' => '1930',
				'ENGINE' => 'MyISAM',
				'TABLE_COLLATION' => 'latin1_general_ci'
			)
		));
	}

}
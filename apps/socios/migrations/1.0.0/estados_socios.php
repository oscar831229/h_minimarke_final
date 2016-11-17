<?php 

class EstadosSociosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('estados_socios', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 60,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('accion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'nombre'
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
				'AUTO_INCREMENT' => '9',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
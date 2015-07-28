<?php 

class MagnitudesMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('magnitudes', array(
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
					'size' => 80,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('unidad_base', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
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
				'AUTO_INCREMENT' => '6',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_general_ci'
			)
		));
	}

}
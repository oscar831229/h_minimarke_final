<?php 

class MagforMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('magfor', array(
			'columns' => array(
				new DbColumn('codfor', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_TEXT,
					'notNull' => true,
					'after' => 'codfor'
				)),
				new DbColumn('version', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'nombre'
				)),
				new DbColumn('termen', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'after' => 'version'
				)),
				new DbColumn('terexti', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'after' => 'termen'
				)),
				new DbColumn('terextf', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'after' => 'terexti'
				)),
				new DbColumn('ternom', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 120,
					'after' => 'terextf'
				)),
				new DbColumn('minimo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 16,
					'scale' => 2,
					'after' => 'ternom'
				)),
				new DbColumn('campo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 5,
					'notNull' => true,
					'after' => 'minimo'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'codfor'
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
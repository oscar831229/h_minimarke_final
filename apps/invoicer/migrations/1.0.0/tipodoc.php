<?php 

class TipodocMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('tipodoc', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 70,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('clase', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'nombre'
				)),
				new DbColumn('predeterminado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'clase'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'codigo'
				)),
				new DbIndex('clase', array(
					'clase'
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
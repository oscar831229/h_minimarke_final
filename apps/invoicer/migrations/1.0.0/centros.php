<?php 

class CentrosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('centros', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nom_centro', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 50,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('responsable', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'nom_centro'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'responsable'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'codigo'
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
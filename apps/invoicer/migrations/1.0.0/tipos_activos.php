<?php 

class TiposActivosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('tipos_activos', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 50,
					'notNull' => true,
					'after' => 'codigo'
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
				'AUTO_INCREMENT' => '5',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
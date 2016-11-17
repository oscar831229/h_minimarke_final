<?php 

class TipoSociosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('tipo_socios', array(
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
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 60,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('cuota_minima', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 15,
					'scale' => 0,
					'notNull' => true,
					'after' => 'nombre'
				)),
				new DbColumn('mora_cuota', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 2,
					'notNull' => true,
					'after' => 'cuota_minima'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'mora_cuota'
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
				'AUTO_INCREMENT' => '13',
				'ENGINE' => 'MyISAM',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
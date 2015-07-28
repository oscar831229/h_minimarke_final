<?php 

class EstudiosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('estudios', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('institucion', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 60,
					'after' => 'socios_id'
				)),
				new DbColumn('ciudad', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'institucion'
				)),
				new DbColumn('fecha_grado', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'ciudad'
				)),
				new DbColumn('titulo', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 50,
					'after' => 'fecha_grado'
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
				'AUTO_INCREMENT' => '1',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
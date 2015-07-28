<?php 

class CriteriosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('criterios', array(
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
					'size' => 70,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('puntaje', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'nombre'
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'puntaje'
				)),
				new DbColumn('tipo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'descripcion'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'tipo'
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
				'AUTO_INCREMENT' => '20',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
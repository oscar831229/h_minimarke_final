<?php 

class GrabMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('grab', array(
			'columns' => array(
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'comprob'
				)),
				new DbColumn('accion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'numero'
				)),
				new DbColumn('fecha_grab', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'accion'
				)),
				new DbColumn('hora_grab', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'after' => 'fecha_grab'
				)),
				new DbColumn('codigo_grab', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'hora_grab'
				))
			),
			'indexes' => array(
				new DbIndex('l_grab', array(
					'comprob',
					'numero',
					'accion'
				)),
				new DbIndex('l_grabf', array(
					'fecha_grab'
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
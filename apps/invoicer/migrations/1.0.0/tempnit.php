<?php 

class TempnitMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('tempnit', array(
			'columns' => array(
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('clase', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'nit'
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 50,
					'after' => 'clase'
				)),
				new DbColumn('direccion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 25,
					'after' => 'nombre'
				)),
				new DbColumn('ciudad', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'direccion'
				)),
				new DbColumn('telefono', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'ciudad'
				)),
				new DbColumn('otros', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'telefono'
				))
			),
			'indexes' => array(
				new DbIndex('l_tempnit', array(
					'nit'
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
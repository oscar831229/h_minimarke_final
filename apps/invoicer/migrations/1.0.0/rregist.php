<?php 

class RregistMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rregist', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('tarjeta', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('ingreso', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'tarjeta'
				)),
				new DbColumn('origen', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'ingreso'
				)),
				new DbColumn('via', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'after' => 'origen'
				)),
				new DbColumn('destino', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'via'
				)),
				new DbColumn('vaucher', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'destino'
				)),
				new DbColumn('salida', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'vaucher'
				))
			),
			'indexes' => array(
				new DbIndex('l_rregist', array(
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
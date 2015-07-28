<?php 

class RhabitaMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rhabita', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('tipohb', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'codigo'
				)),
				new DbColumn('numks', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'tipohb'
				)),
				new DbColumn('numdb', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'numks'
				)),
				new DbColumn('numsd', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'numdb'
				)),
				new DbColumn('numsc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'numsd'
				)),
				new DbColumn('numnn', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'numsc'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'numnn'
				)),
				new DbColumn('nreser', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'estado'
				))
			),
			'indexes' => array(
				new DbIndex('l_rhabita', array(
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
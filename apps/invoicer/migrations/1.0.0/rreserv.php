<?php 

class RreservMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rreserv', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'codigo'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('nitemp', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'nit'
				)),
				new DbColumn('fpago', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'nitemp'
				)),
				new DbColumn('fecha_i', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'fpago'
				)),
				new DbColumn('fecha_f', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'fecha_i'
				)),
				new DbColumn('numadu', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'fecha_f'
				)),
				new DbColumn('numnin', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'numadu'
				)),
				new DbColumn('numadi', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'numnin'
				)),
				new DbColumn('motivo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'numadi'
				)),
				new DbColumn('segmen', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'motivo'
				)),
				new DbColumn('planes', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'after' => 'segmen'
				)),
				new DbColumn('habita', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'planes'
				)),
				new DbColumn('vended', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'habita'
				)),
				new DbColumn('observ', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 60,
					'after' => 'vended'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'observ'
				))
			),
			'indexes' => array(
				new DbIndex('l_rreserv', array(
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
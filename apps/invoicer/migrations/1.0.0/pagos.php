<?php 

class PagosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('pagos', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('concepto', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('fecha_i', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'concepto'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'fecha_i'
				)),
				new DbColumn('fecha_pago', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'valor'
				)),
				new DbColumn('fecha_f', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'fecha_pago'
				)),
				new DbColumn('valor2', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'fecha_f'
				))
			),
			'indexes' => array(
				new DbIndex('l_pagos', array(
					'codigo',
					'concepto',
					'fecha_i'
				)),
				new DbIndex('l_pagos1', array(
					'concepto',
					'codigo',
					'fecha_i'
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
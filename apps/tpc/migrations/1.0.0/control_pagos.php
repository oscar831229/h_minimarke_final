<?php 

class ControlPagosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('control_pagos', array(
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
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('pagado', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'socios_id'
				)),
				new DbColumn('dias_pagado', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'pagado'
				)),
				new DbColumn('capital', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'dias_pagado'
				)),
				new DbColumn('interes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'capital'
				)),
				new DbColumn('dias_corriente', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'interes'
				)),
				new DbColumn('mora', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'dias_corriente'
				)),
				new DbColumn('dias_mora', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'mora'
				)),
				new DbColumn('fecha_pago', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'dias_mora'
				)),
				new DbColumn('saldo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'fecha_pago'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'saldo'
				)),
				new DbColumn('recibos_pagos_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'estado'
				)),
				new DbColumn('nota_contable_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'recibos_pagos_id'
				)),
				new DbColumn('rc', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'after' => 'nota_contable_id'
				)),
				new DbColumn('nota_historia_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'rc'
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
				'AUTO_INCREMENT' => '1587',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
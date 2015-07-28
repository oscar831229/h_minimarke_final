<?php 

class RecibosPagosMigration_105 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('recibos_pagos', array(
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
				new DbColumn('recibo_provisional', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'after' => 'id'
				)),
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'recibo_provisional'
				)),
				new DbColumn('ciudad_pago', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('fecha_pago', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'ciudad_pago'
				)),
				new DbColumn('fecha_recibo', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'fecha_pago'
				)),
				new DbColumn('valor_pagado', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'fecha_recibo'
				)),
				new DbColumn('valor_reserva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'valor_pagado'
				)),
				new DbColumn('valor_cuoact', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'valor_reserva'
				)),
				new DbColumn('valor_cuoafi', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'valor_cuoact'
				)),
				new DbColumn('valor_capital', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'valor_cuoafi'
				)),
				new DbColumn('valor_interesc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'valor_capital'
				)),
				new DbColumn('valor_interesm', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'valor_interesc'
				)),
				new DbColumn('valor_inicial', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'valor_interesm'
				)),
				new DbColumn('valor_financiacion', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'valor_inicial'
				)),
				new DbColumn('cuentas_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'valor_financiacion'
				)),
				new DbColumn('otros', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'cuentas_id'
				)),
				new DbColumn('observaciones', array(
					'type' => DbColumn::TYPE_TEXT,
					'notNull' => true,
					'after' => 'otros'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'observaciones'
				)),
				new DbColumn('aplico', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'estado'
				)),
				new DbColumn('rc', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'after' => 'aplico'
				)),
				new DbColumn('pago_posterior', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'rc'
				)),
				new DbColumn('abono_reservas_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'after' => 'pago_posterior'
				)),
				new DbColumn('cuota_saldo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'after' => 'abono_reservas_id'
				)),
				new DbColumn('calculos', array(
					'type' => DbColumn::TYPE_TEXT,
					'notNull' => true,
					'after' => 'cuota_saldo'
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
				'AUTO_INCREMENT' => '82',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
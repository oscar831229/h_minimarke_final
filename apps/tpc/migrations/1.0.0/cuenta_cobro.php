<?php 

class CuentaCobroMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('cuenta_cobro', array(
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
				new DbColumn('referencia', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'socios_id'
				)),
				new DbColumn('fecha_corte', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'referencia'
				)),
				new DbColumn('periodo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'after' => 'fecha_corte'
				)),
				new DbColumn('fecha_limite_pago', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'notNull' => true,
					'after' => 'periodo'
				)),
				new DbColumn('pago_minimo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'fecha_limite_pago'
				)),
				new DbColumn('pago_total_cancele', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'pago_minimo'
				)),
				new DbColumn('valor_cuota_inicial', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'pago_total_cancele'
				)),
				new DbColumn('valor_cuota_pagar', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'valor_cuota_inicial'
				)),
				new DbColumn('valor_interesm', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'valor_cuota_pagar'
				)),
				new DbColumn('dias_mora', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'valor_interesm'
				)),
				new DbColumn('valor_interesc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'dias_mora'
				)),
				new DbColumn('dias_corriente', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'valor_interesc'
				)),
				new DbColumn('valor_capital', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'dias_corriente'
				)),
				new DbColumn('cuotas_plazo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'valor_capital'
				)),
				new DbColumn('cuota', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'cuotas_plazo'
				)),
				new DbColumn('cuotas_pendientes', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'cuota'
				)),
				new DbColumn('tasa', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 2,
					'after' => 'cuotas_pendientes'
				)),
				new DbColumn('valor_membresia', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'tasa'
				)),
				new DbColumn('valor_inicial', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'valor_membresia'
				)),
				new DbColumn('saldo_financiar', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'valor_inicial'
				)),
				new DbColumn('saldo_total_plazos', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'saldo_financiar'
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
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
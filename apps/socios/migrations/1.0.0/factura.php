<?php 

class FacturaMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('factura', array(
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
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'numero'
				)),
				new DbColumn('movimiento_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('fecha_factura', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'movimiento_id'
				)),
				new DbColumn('periodo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'notNull' => true,
					'after' => 'fecha_factura'
				)),
				new DbColumn('fecha_vencimiento', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'periodo'
				)),
				new DbColumn('saldo_vencido', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'fecha_vencimiento'
				)),
				new DbColumn('saldo_mora', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'saldo_vencido'
				)),
				new DbColumn('dias_mora', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'saldo_mora'
				)),
				new DbColumn('mora_pagado', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'dias_mora'
				)),
				new DbColumn('cuota_vigente', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'mora_pagado'
				)),
				new DbColumn('vigente_pagado', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'cuota_vigente'
				)),
				new DbColumn('total_factura', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'vigente_pagado'
				)),
				new DbColumn('val_ult_abono', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'total_factura'
				)),
				new DbColumn('fec_ult_abono', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'val_ult_abono'
				)),
				new DbColumn('sal_ant_neto', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'fec_ult_abono'
				)),
				new DbColumn('sal_ant_interes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'sal_ant_neto'
				)),
				new DbColumn('cargo_mes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'sal_ant_interes'
				)),
				new DbColumn('sal_actual', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'cargo_mes'
				)),
				new DbColumn('sal_act_mora', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'sal_actual'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'sal_act_mora'
				)),
				new DbColumn('invoicer_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'estado'
				)),
				new DbColumn('comprob_contab', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 4,
					'after' => 'invoicer_id'
				)),
				new DbColumn('numero_contab', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 4,
					'after' => 'comprob_contab'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('factura_1_index', array(
					'socios_id'
				)),
				new DbIndex('factura_2_index', array(
					'socios_id',
					'periodo'
				)),
				new DbIndex('factura_3_index', array(
					'periodo',
					'estado'
				)),
				new DbIndex('socios_12_index', array(
					'socios_id'
				)),
				new DbIndex('socios_13_index', array(
					'estado'
				)),
				new DbIndex('socios_14_index', array(
					'socios_id',
					'periodo'
				)),
				new DbIndex('socios_15_index', array(
					'socios_id',
					'periodo',
					'estado'
				)),
				new DbIndex('socios_16_index', array(
					'periodo',
					'estado'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '2477',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
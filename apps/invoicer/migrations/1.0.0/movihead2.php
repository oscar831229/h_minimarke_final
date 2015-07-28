<?php 

class Movihead2Migration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('movihead2', array(
			'columns' => array(
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('almacen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'comprob'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'after' => 'almacen'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'numero'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'fecha'
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'after' => 'nit'
				)),
				new DbColumn('n_pedido', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'centro_costo'
				)),
				new DbColumn('f_vence', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'n_pedido'
				)),
				new DbColumn('f_expira', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'f_vence'
				)),
				new DbColumn('f_entrega', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'f_expira'
				)),
				new DbColumn('forma_pago', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'f_entrega'
				)),
				new DbColumn('almacen_destino', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'forma_pago'
				)),
				new DbColumn('iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'almacen_destino'
				)),
				new DbColumn('ivad', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'iva'
				)),
				new DbColumn('ivam', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'ivad'
				)),
				new DbColumn('ica', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'ivam'
				)),
				new DbColumn('descuento', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'ica'
				)),
				new DbColumn('retencion', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'descuento'
				)),
				new DbColumn('saldo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'retencion'
				)),
				new DbColumn('vendedor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'after' => 'saldo'
				)),
				new DbColumn('factura_c', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'vendedor'
				)),
				new DbColumn('solicita', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'factura_c'
				)),
				new DbColumn('autoriza', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'solicita'
				)),
				new DbColumn('nota', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'autoriza'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'nota'
				)),
				new DbColumn('v_total', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'estado'
				))
			),
			'indexes' => array(
				new DbIndex('l_movihead2', array(
					'comprob',
					'almacen',
					'numero'
				)),
				new DbIndex('l_doc2', array(
					'comprob',
					'numero'
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
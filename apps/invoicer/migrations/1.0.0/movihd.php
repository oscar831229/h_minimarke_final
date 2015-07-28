<?php 

class MovihdMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('movihd', array(
			'columns' => array(
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'after' => 'comprob'
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
				new DbColumn('n_pedido', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'nit'
				)),
				new DbColumn('n_mesa', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 6,
					'after' => 'n_pedido'
				)),
				new DbColumn('ubica', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'n_mesa'
				)),
				new DbColumn('c_cajero', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'ubica'
				)),
				new DbColumn('c_mesero', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'c_cajero'
				)),
				new DbColumn('hora', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'c_mesero'
				)),
				new DbColumn('n_personas', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 6,
					'after' => 'hora'
				)),
				new DbColumn('n_habita', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'n_personas'
				)),
				new DbColumn('f_vence', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'n_habita'
				)),
				new DbColumn('forma_pago', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'f_vence'
				)),
				new DbColumn('iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'forma_pago'
				)),
				new DbColumn('ivad', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'iva'
				)),
				new DbColumn('saldo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'ivad'
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
				new DbColumn('nota', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'factura_c'
				)),
				new DbColumn('v_total', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'nota'
				))
			),
			'indexes' => array(
				new DbIndex('l_movihd', array(
					'comprob',
					'numero'
				)),
				new DbIndex('l_movihd1', array(
					'n_mesa'
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
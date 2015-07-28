<?php 

class InveMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('inve', array(
			'columns' => array(
				new DbColumn('item', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 70,
					'notNull' => true,
					'after' => 'item'
				)),
				new DbColumn('linea', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'notNull' => true,
					'after' => 'descripcion'
				)),
				new DbColumn('unidad', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'after' => 'linea'
				)),
				new DbColumn('peso', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 6,
					'after' => 'unidad'
				)),
				new DbColumn('volumen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 6,
					'after' => 'peso'
				)),
				new DbColumn('plazo_reposicion', array(
					'type' => DbColumn::TYPE_INTEGER,
					'after' => 'volumen'
				)),
				new DbColumn('producto', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 2,
					'after' => 'plazo_reposicion'
				)),
				new DbColumn('saldo_actual', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'producto'
				)),
				new DbColumn('fisico', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 6,
					'after' => 'saldo_actual'
				)),
				new DbColumn('costo_actual', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'fisico'
				)),
				new DbColumn('precio_compra', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 6,
					'after' => 'costo_actual'
				)),
				new DbColumn('f_u_compra', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'precio_compra'
				)),
				new DbColumn('precio_venta_m', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'f_u_compra'
				)),
				new DbColumn('f_u_venta', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'precio_venta_m'
				)),
				new DbColumn('iva', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'unsigned' => true,
					'after' => 'f_u_venta'
				)),
				new DbColumn('iva_venta', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 4,
					'unsigned' => true,
					'after' => 'iva'
				)),
				new DbColumn('por_recibir', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 6,
					'after' => 'iva_venta'
				)),
				new DbColumn('por_entregar', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 6,
					'after' => 'por_recibir'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'por_entregar'
				)),
				new DbColumn('unidad_porcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'estado'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'item'
				)),
				new DbIndex('l_inve1', array(
					'linea',
					'item'
				)),
				new DbIndex('estado', array(
					'estado',
					'descripcion'
				)),
				new DbIndex('estado_2', array(
					'estado',
					'descripcion'
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
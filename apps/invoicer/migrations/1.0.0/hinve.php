<?php 

class HinveMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('hinve', array(
			'columns' => array(
				new DbColumn('item', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 60,
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
					'after' => 'linea'
				)),
				new DbColumn('minimo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 6,
					'after' => 'unidad'
				)),
				new DbColumn('maximo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 6,
					'after' => 'minimo'
				)),
				new DbColumn('peso', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 6,
					'after' => 'maximo'
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
					'scale' => 6,
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
					'scale' => 6,
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
					'scale' => 6,
					'after' => 'f_u_compra'
				)),
				new DbColumn('f_u_venta', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'precio_venta_m'
				)),
				new DbColumn('iva', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 6,
					'after' => 'f_u_venta'
				)),
				new DbColumn('por_recibir', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 6,
					'after' => 'iva'
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
					'after' => 'por_entregar'
				))
			),
			'indexes' => array(

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
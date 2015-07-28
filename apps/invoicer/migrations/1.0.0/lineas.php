<?php 

class LineasMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('lineas', array(
			'columns' => array(
				new DbColumn('almacen', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('linea', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'primary' => true,
					'notNull' => true,
					'after' => 'almacen'
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 50,
					'notNull' => true,
					'after' => 'linea'
				)),
				new DbColumn('es_auxiliar', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'nombre'
				)),
				new DbColumn('cta_compra', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'es_auxiliar'
				)),
				new DbColumn('cta_venta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_compra'
				)),
				new DbColumn('cta_consumo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_venta'
				)),
				new DbColumn('cta_descuento', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_consumo'
				)),
				new DbColumn('cta_inve', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_descuento'
				)),
				new DbColumn('cta_costo_venta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_inve'
				)),
				new DbColumn('cta_ret_compra', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_costo_venta'
				)),
				new DbColumn('porc_compra', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 3,
					'after' => 'cta_ret_compra'
				)),
				new DbColumn('minimo_ret', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'porc_compra'
				)),
				new DbColumn('cta_dev_ventas', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'minimo_ret'
				)),
				new DbColumn('cta_dev_compras', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_dev_ventas'
				)),
				new DbColumn('cta_hortic', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_dev_compras'
				)),
				new DbColumn('porc_hortic', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 3,
					'after' => 'cta_hortic'
				)),
				new DbColumn('minimo_v', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 0,
					'after' => 'porc_hortic'
				)),
				new DbColumn('minimo_c', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 0,
					'after' => 'minimo_v'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'almacen',
					'linea'
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
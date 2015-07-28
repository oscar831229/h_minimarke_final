<?php 

class GruposMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('grupos', array(
			'columns' => array(
				new DbColumn('linea', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'primary' => true,
					'notNull' => true,
					'first' => true
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
					'after' => 'nombre'
				)),
				new DbColumn('cta_compra', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'es_auxiliar'
				)),
				new DbColumn('cta_inve', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_compra'
				)),
				new DbColumn('cta_ret_compra', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_inve'
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
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'linea'
				)),
				new DbIndex('cta_compra', array(
					'cta_compra'
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
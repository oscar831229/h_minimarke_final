<?php 

class RecetapMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('recetap', array(
			'columns' => array(
				new DbColumn('almacen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'first' => true
				)),
				new DbColumn('numero_rec', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'after' => 'almacen'
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'numero_rec'
				)),
				new DbColumn('num_personas', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'notNull' => true,
					'after' => 'nombre'
				)),
				new DbColumn('tipo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'num_personas'
				)),
				new DbColumn('porc_varios', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 2,
					'after' => 'tipo'
				)),
				new DbColumn('precio_venta', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'porc_varios'
				)),
				new DbColumn('iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'precio_venta'
				)),
				new DbColumn('precio_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'iva'
				)),
				new DbColumn('porc_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 2,
					'after' => 'precio_costo'
				)),
				new DbColumn('costoent', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'porc_costo'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'costoent'
				))
			),
			'indexes' => array(
				new DbIndex('ix199_1', array(
					'numero_rec'
				)),
				new DbIndex('l_recetap', array(
					'almacen',
					'numero_rec'
				)),
				new DbIndex('ix199_2', array(
					'nombre'
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
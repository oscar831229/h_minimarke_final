<?php 

class Movilin2Migration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('movilin2', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('almacen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'notNull' => true,
					'after' => 'comprob'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'almacen'
				)),
				new DbColumn('num_linea', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 6,
					'after' => 'numero'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'num_linea'
				)),
				new DbColumn('almacen_destino', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('item', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'notNull' => true,
					'after' => 'almacen_destino'
				)),
				new DbColumn('cantidad', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 16,
					'scale' => 6,
					'notNull' => true,
					'after' => 'item'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 16,
					'scale' => 6,
					'after' => 'cantidad'
				)),
				new DbColumn('cantidad_rec', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 16,
					'scale' => 6,
					'after' => 'valor'
				)),
				new DbColumn('cantidad_desp', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 16,
					'scale' => 6,
					'after' => 'cantidad_rec'
				)),
				new DbColumn('costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'cantidad_desp'
				)),
				new DbColumn('nota', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'costo'
				)),
				new DbColumn('prioridad', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'nota'
				)),
				new DbColumn('iva', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 6,
					'after' => 'prioridad'
				)),
				new DbColumn('descuento', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 2,
					'after' => 'iva'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('l1_movilin2', array(
					'almacen_destino',
					'item',
					'fecha'
				)),
				new DbIndex('l_auxi', array(
					'item',
					'fecha',
					'prioridad'
				)),
				new DbIndex('l_docl', array(
					'comprob',
					'numero'
				)),
				new DbIndex('l_movilin21', array(
					'comprob',
					'item'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '67606',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
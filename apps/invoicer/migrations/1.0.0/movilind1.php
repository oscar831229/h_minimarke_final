<?php 

class Movilind1Migration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('movilind1', array(
			'columns' => array(
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'comprob'
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
				new DbColumn('item', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'after' => 'item'
				)),
				new DbColumn('dependen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'after' => 'centro_costo'
				)),
				new DbColumn('n_comanda', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 6,
					'after' => 'dependen'
				)),
				new DbColumn('cantidad', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 4,
					'notNull' => true,
					'after' => 'n_comanda'
				)),
				new DbColumn('cantidad_rec', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 4,
					'notNull' => true,
					'after' => 'cantidad'
				)),
				new DbColumn('cantidadt', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 4,
					'after' => 'cantidad_rec'
				)),
				new DbColumn('cantidadt_rec', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 4,
					'after' => 'cantidadt'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'cantidadt_rec'
				)),
				new DbColumn('costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'valor'
				)),
				new DbColumn('porcdesc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 2,
					'after' => 'costo'
				)),
				new DbColumn('centro_destino', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'after' => 'porcdesc'
				)),
				new DbColumn('dependendes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'after' => 'centro_destino'
				)),
				new DbColumn('nota', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'dependendes'
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
				))
			),
			'indexes' => array(
				new DbIndex('l1_movilind1', array(
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
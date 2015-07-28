<?php 

class Movil1Migration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('movil1', array(
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
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'fecha'
				)),
				new DbColumn('cta_iva', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cuenta'
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'notNull' => true,
					'after' => 'cta_iva'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'centro_costo'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'nit'
				)),
				new DbColumn('costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'valor'
				))
			),
			'indexes' => array(
				new DbIndex('l_movil1', array(
					'comprob',
					'numero',
					'num_linea'
				)),
				new DbIndex('l_movil12', array(
					'cuenta',
					'centro_costo'
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
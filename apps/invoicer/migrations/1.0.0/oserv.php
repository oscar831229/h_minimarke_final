<?php 

class OservMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('oserv', array(
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
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'notNull' => true,
					'after' => 'numero'
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'notNull' => true,
					'after' => 'nit'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'centro_costo'
				)),
				new DbColumn('item', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'item'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'notNull' => true,
					'after' => 'descripcion'
				)),
				new DbColumn('nota', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'valor'
				)),
				new DbColumn('num_fac', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'nota'
				))
			),
			'indexes' => array(
				new DbIndex('l_oserv', array(
					'comprob',
					'numero'
				)),
				new DbIndex('l_oserv1', array(
					'item',
					'fecha'
				)),
				new DbIndex('l_oserv2', array(
					'item',
					'nit',
					'fecha'
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
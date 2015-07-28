<?php 

class VendedorMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('vendedor', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nom_vendedor', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('tipo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'nom_vendedor'
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'notNull' => true,
					'after' => 'tipo'
				)),
				new DbColumn('zona', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'after' => 'centro_costo'
				)),
				new DbColumn('sector', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'after' => 'zona'
				)),
				new DbColumn('porc_vendv', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 2,
					'after' => 'sector'
				)),
				new DbColumn('porc_vendr', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 2,
					'after' => 'porc_vendv'
				))
			),
			'indexes' => array(
				new DbIndex('l_vendedor', array(
					'codigo'
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
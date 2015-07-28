<?php 

class CarteraMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('cartera', array(
			'columns' => array(
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'cuenta'
				)),
				new DbColumn('tipo_doc', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'primary' => true,
					'notNull' => true,
					'after' => 'nit'
				)),
				new DbColumn('numero_doc', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'after' => 'tipo_doc'
				)),
				new DbColumn('vendedor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'notNull' => true,
					'after' => 'numero_doc'
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'after' => 'vendedor'
				)),
				new DbColumn('f_emision', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'centro_costo'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'f_emision'
				)),
				new DbColumn('saldo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'notNull' => true,
					'after' => 'valor'
				)),
				new DbColumn('f_vence', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'saldo'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'f_vence'
				))
			),
			'indexes' => array(
				new DbIndex('lv_cartera', array(
					'cuenta',
					'nit',
					'tipo_doc',
					'numero_doc'
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
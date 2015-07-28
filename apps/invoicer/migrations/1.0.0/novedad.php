<?php 

class NovedadMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('novedad', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('concepto', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('secuencia', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'concepto'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'secuencia'
				)),
				new DbColumn('veces', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'valor'
				)),
				new DbColumn('accion', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'after' => 'veces'
				)),
				new DbColumn('periodicidad', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'after' => 'accion'
				)),
				new DbColumn('fecha_i', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'periodicidad'
				)),
				new DbColumn('fecha_f', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'fecha_i'
				)),
				new DbColumn('clase_p', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'after' => 'fecha_f'
				)),
				new DbColumn('numero_p', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'clase_p'
				)),
				new DbColumn('por_retiro', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'numero_p'
				))
			),
			'indexes' => array(
				new DbIndex('l_novedad', array(
					'codigo',
					'concepto',
					'secuencia'
				)),
				new DbIndex('l_novedad1', array(
					'concepto',
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
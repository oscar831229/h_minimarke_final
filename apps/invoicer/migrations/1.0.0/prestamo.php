<?php 

class PrestamoMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('prestamo', array(
			'columns' => array(
				new DbColumn('clase', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'primary' => true,
					'notNull' => true,
					'after' => 'clase'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('nota', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'after' => 'numero'
				)),
				new DbColumn('cuota', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'nota'
				)),
				new DbColumn('valor_n', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'cuota'
				)),
				new DbColumn('valor_t', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'valor_n'
				)),
				new DbColumn('saldo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'valor_t'
				)),
				new DbColumn('quincena', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'after' => 'saldo'
				)),
				new DbColumn('fecha_i', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'quincena'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'fecha_i'
				))
			),
			'indexes' => array(
				new DbIndex('l_prestamo', array(
					'clase',
					'codigo',
					'numero'
				)),
				new DbIndex('l_prestam1', array(
					'codigo',
					'clase',
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
<?php 

class KontcMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('kontc', array(
			'columns' => array(
				new DbColumn('informe', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('linea', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'notNull' => true,
					'after' => 'informe'
				)),
				new DbColumn('tipo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'linea'
				)),
				new DbColumn('salto', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'notNull' => true,
					'after' => 'tipo'
				)),
				new DbColumn('pagina', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'salto'
				)),
				new DbColumn('columna', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'after' => 'pagina'
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 50,
					'after' => 'columna'
				)),
				new DbColumn('saldo_a', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 16,
					'after' => 'descripcion'
				)),
				new DbColumn('debe', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 16,
					'after' => 'saldo_a'
				)),
				new DbColumn('haber', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 16,
					'after' => 'debe'
				)),
				new DbColumn('saldo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 16,
					'after' => 'haber'
				)),
				new DbColumn('cuenta_i', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'saldo'
				)),
				new DbColumn('cuenta_f', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cuenta_i'
				)),
				new DbColumn('operador', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'cuenta_f'
				))
			),
			'indexes' => array(
				new DbIndex('l_kontc', array(
					'informe',
					'linea',
					'cuenta_i'
				)),
				new DbIndex('l_kontc1', array(
					'informe',
					'tipo',
					'cuenta_f'
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
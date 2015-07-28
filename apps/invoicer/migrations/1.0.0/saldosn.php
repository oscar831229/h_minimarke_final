<?php 

class SaldosnMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('saldosn', array(
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
				new DbColumn('ano_mes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'nit'
				)),
				new DbColumn('debe', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'notNull' => true,
					'after' => 'ano_mes'
				)),
				new DbColumn('haber', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'notNull' => true,
					'after' => 'debe'
				)),
				new DbColumn('saldo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'notNull' => true,
					'after' => 'haber'
				)),
				new DbColumn('base_grab', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'saldo'
				))
			),
			'indexes' => array(
				new DbIndex('l_saldosn', array(
					'cuenta',
					'nit',
					'ano_mes'
				)),
				new DbIndex('l_anon', array(
					'ano_mes'
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
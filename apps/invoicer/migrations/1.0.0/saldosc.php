<?php 

class SaldoscMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('saldosc', array(
			'columns' => array(
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('ano_mes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'cuenta'
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
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'cuenta',
					'ano_mes'
				)),
				new DbIndex('l_anoc', array(
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
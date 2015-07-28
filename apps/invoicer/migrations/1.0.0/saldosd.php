<?php 

class SaldosdMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('saldosd', array(
			'columns' => array(
				new DbColumn('item', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'item'
				)),
				new DbColumn('dependen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'centro_costo'
				)),
				new DbColumn('ano_mes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'dependen'
				)),
				new DbColumn('saldot', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'ano_mes'
				)),
				new DbColumn('saldo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'saldot'
				)),
				new DbColumn('costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'saldo'
				))
			),
			'indexes' => array(
				new DbIndex('l_saldosd', array(
					'item',
					'centro_costo',
					'dependen',
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
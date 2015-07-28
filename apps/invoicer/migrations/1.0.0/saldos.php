<?php 

class SaldosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('saldos', array(
			'columns' => array(
				new DbColumn('item', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('almacen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'item'
				)),
				new DbColumn('ano_mes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'almacen'
				)),
				new DbColumn('saldo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 16,
					'scale' => 6,
					'after' => 'ano_mes'
				)),
				new DbColumn('costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 16,
					'scale' => 2,
					'after' => 'saldo'
				)),
				new DbColumn('f_u_mov', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'costo'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'item',
					'almacen',
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
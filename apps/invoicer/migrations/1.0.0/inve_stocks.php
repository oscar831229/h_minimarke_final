<?php 

class InveStocksMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('inve_stocks', array(
			'columns' => array(
				new DbColumn('item', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('almacen', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'after' => 'item'
				)),
				new DbColumn('minimo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'almacen'
				)),
				new DbColumn('maximo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'minimo'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'item',
					'almacen'
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
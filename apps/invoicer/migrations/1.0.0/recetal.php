<?php 

class RecetalMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('recetal', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('tipol', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'id'
				)),
				new DbColumn('almacen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'tipol'
				)),
				new DbColumn('numero_rec', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'almacen'
				)),
				new DbColumn('item', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'notNull' => true,
					'after' => 'numero_rec'
				)),
				new DbColumn('divisor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'notNull' => true,
					'after' => 'item'
				)),
				new DbColumn('cantidad', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 2,
					'notNull' => true,
					'after' => 'divisor'
				)),
				new DbColumn('valore', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'cantidad'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'valore'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('l_recetal', array(
					'almacen',
					'numero_rec',
					'item'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '5147',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
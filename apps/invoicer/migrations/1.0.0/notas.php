<?php 

class NotasMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('notas', array(
			'columns' => array(
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('almacen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'comprob'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'almacen'
				)),
				new DbColumn('num_linea', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 6,
					'after' => 'numero'
				)),
				new DbColumn('item', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'after' => 'num_linea'
				)),
				new DbColumn('nota', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 240,
					'after' => 'item'
				))
			),
			'indexes' => array(
				new DbIndex('l_notas', array(
					'comprob',
					'almacen',
					'numero',
					'item',
					'num_linea'
				)),
				new DbIndex('l_not', array(
					'comprob',
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
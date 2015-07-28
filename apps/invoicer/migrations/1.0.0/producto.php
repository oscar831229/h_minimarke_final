<?php 

class ProductoMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('producto', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 6,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nom_producto', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'codigo'
				))
			),
			'indexes' => array(
				new DbIndex('llave_producto', array(
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
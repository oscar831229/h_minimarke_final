<?php 

class MatrizProveedoresMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('matriz_proveedores', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('item', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'notNull' => true,
					'after' => 'item'
				)),
				new DbColumn('preferencia', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'nit'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '1',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
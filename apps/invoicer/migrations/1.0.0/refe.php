<?php 

class RefeMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('refe', array(
			'columns' => array(
				new DbColumn('item', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 35,
					'after' => 'item'
				)),
				new DbColumn('linea', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'notNull' => true,
					'after' => 'descripcion'
				))
			),
			'indexes' => array(
				new DbIndex('l_refe', array(
					'item'
				)),
				new DbIndex('l_refe1', array(
					'linea',
					'item'
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
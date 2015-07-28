<?php 

class QxmenuMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('qxmenu', array(
			'columns' => array(
				new DbColumn('menu', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 4,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('opcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'primary' => true,
					'notNull' => true,
					'after' => 'menu'
				)),
				new DbColumn('detalle', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 50,
					'notNull' => true,
					'after' => 'opcion'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'menu',
					'opcion'
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
<?php 

class QxtmenuMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('qxtmenu', array(
			'columns' => array(
				new DbColumn('menu', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 4,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('detalle', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 50,
					'after' => 'menu'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'menu'
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
<?php 

class MagcamMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('magcam', array(
			'columns' => array(
				new DbColumn('codfor', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('campo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 5,
					'primary' => true,
					'notNull' => true,
					'after' => 'codfor'
				)),
				new DbColumn('posicion', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'unsigned' => true,
					'after' => 'campo'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'codfor',
					'campo'
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
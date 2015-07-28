<?php 

class FormasMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('formas', array(
			'columns' => array(
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'first' => true
				)),
				new DbColumn('linea', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 145,
					'after' => 'comprob'
				))
			),
			'indexes' => array(

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
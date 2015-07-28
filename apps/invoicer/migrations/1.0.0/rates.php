<?php 

class RatesMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rates', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('ip', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'id'
				)),
				new DbColumn('time', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 13,
					'after' => 'ip'
				)),
				new DbColumn('rating', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'time'
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
				'ENGINE' => 'MyISAM',
				'TABLE_COLLATION' => 'latin1_general_ci'
			)
		));
	}

}
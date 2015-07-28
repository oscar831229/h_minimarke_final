<?php 

class UsersMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('users', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('login', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 20,
					'after' => 'id'
				)),
				new DbColumn('pass', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 35,
					'after' => 'login'
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
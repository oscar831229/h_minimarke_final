<?php 

class ConfigurationMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('configuration', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('application', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 2,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('name', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 32,
					'notNull' => true,
					'after' => 'application'
				)),
				new DbColumn('value', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'name'
				)),
				new DbColumn('tipo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 16,
					'notNull' => true,
					'after' => 'value'
				)),
				new DbColumn('description', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'tipo'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('application', array(
					'application',
					'name'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '18',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
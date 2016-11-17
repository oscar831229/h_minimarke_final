<?php 

class ConsolidadosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('consolidados', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('server', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 32,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('instance', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 64,
					'notNull' => true,
					'after' => 'server'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'instance'
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
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
<?php 

class ParentescosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('parentescos', array(
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
				new DbColumn('tipo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 2,
					'after' => 'id'
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 60,
					'notNull' => true,
					'after' => 'tipo'
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
				'AUTO_INCREMENT' => '11',
				'ENGINE' => 'MyISAM',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
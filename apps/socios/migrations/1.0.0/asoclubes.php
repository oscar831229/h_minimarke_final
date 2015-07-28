<?php 

class AsoclubesMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('asoclubes', array(
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
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('club', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('desde', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 4,
					'after' => 'club'
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
				'AUTO_INCREMENT' => '99548',
				'ENGINE' => 'MyISAM',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
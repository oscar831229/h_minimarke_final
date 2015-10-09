<?php 

class EstadosCivilesMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('estados_civiles', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 5,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'notNull' => true,
					'after' => 'id'
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
				'AUTO_INCREMENT' => '8',
				'ENGINE' => 'MyISAM',
				'TABLE_COLLATION' => 'latin1_spanish_ci'
			)
		));
	}

}
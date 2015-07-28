<?php 

class BancoMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('banco', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 14,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 100,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('oficina', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 50,
					'notNull' => true,
					'after' => 'nombre'
				)),
				new DbColumn('ciudad', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 30,
					'after' => 'oficina'
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
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
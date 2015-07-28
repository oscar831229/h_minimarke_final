<?php 

class EstadosCivilesMigration_105 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('estados_civiles', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 5,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'nombre'
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
				'AUTO_INCREMENT' => '',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
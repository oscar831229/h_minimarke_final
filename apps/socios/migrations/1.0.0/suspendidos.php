<?php 

class SuspendidosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('suspendidos', array(
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
					'size' => 11,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('periodo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('usuarios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'periodo'
				)),
				new DbColumn('observacion', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'usuarios_id'
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
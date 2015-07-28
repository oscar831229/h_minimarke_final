<?php 

class PermisosComprobMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('permisos_comprob', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('usuarios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'after' => 'usuarios_id'
				)),
				new DbColumn('popcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'comprob'
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
				'AUTO_INCREMENT' => '1491',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
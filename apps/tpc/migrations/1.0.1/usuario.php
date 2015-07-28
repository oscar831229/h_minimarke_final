<?php 

class UsuarioMigration_101 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('usuario', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 120,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('login', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 32,
					'notNull' => true,
					'after' => 'nombre'
				)),
				new DbColumn('password', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'login'
				)),
				new DbColumn('roles_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'password'
				)),
				new DbColumn('correoe', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 250,
					'notNull' => true,
					'after' => 'roles_id'
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
				'AUTO_INCREMENT' => '22',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
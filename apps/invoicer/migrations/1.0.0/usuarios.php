<?php 

class UsuariosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('usuarios', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'after' => 'codigo'
				)),
				new DbColumn('clave', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'nombre'
				)),
				new DbColumn('prioridad', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'after' => 'clave'
				)),
				new DbColumn('autoriza', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'prioridad'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'autoriza'
				))
			),
			'indexes' => array(
				new DbIndex('l_usuarios', array(
					'codigo'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
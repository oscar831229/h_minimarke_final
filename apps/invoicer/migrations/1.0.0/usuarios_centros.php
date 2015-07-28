<?php 

class UsuariosCentrosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('usuarios_centros', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('usuario', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'notNull' => true,
					'after' => 'centro_costo'
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
				'AUTO_INCREMENT' => '2',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
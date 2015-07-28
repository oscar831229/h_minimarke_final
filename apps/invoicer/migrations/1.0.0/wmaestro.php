<?php 

class WmaestroMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('wmaestro', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('cedula', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('apellidos', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'notNull' => true,
					'after' => 'cedula'
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'notNull' => true,
					'after' => 'apellidos'
				)),
				new DbColumn('dir_oficina', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 35,
					'after' => 'nombre'
				)),
				new DbColumn('tel_oficina', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'after' => 'dir_oficina'
				)),
				new DbColumn('ciudad_of', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'tel_oficina'
				)),
				new DbColumn('dir_casa', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 35,
					'after' => 'ciudad_of'
				)),
				new DbColumn('tel_casa', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'after' => 'dir_casa'
				)),
				new DbColumn('ciudad', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'tel_casa'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'ciudad'
				)),
				new DbColumn('sexo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'estado'
				)),
				new DbColumn('f_nace', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'sexo'
				)),
				new DbColumn('f_ingreso', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'f_nace'
				)),
				new DbColumn('tipo_socio', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'notNull' => true,
					'after' => 'f_ingreso'
				)),
				new DbColumn('correo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'tipo_socio'
				)),
				new DbColumn('t_credito', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'correo'
				)),
				new DbColumn('profesion', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'notNull' => true,
					'after' => 't_credito'
				)),
				new DbColumn('empresa', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'profesion'
				)),
				new DbColumn('cargo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'empresa'
				))
			),
			'indexes' => array(
				new DbIndex('l_wmaestro', array(
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
<?php 

class SociosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('socios', array(
			'columns' => array(
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('titular_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'socios_id'
				)),
				new DbColumn('numero_accion', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 10,
					'notNull' => true,
					'after' => 'titular_id'
				)),
				new DbColumn('fecha_ingreso', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 10,
					'after' => 'numero_accion'
				)),
				new DbColumn('fecha_inscripcion', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'fecha_ingreso'
				)),
				new DbColumn('tiempo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'fecha_inscripcion'
				)),
				new DbColumn('parentescos_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'tiempo'
				)),
				new DbColumn('nombres', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 60,
					'notNull' => true,
					'after' => 'parentescos_id'
				)),
				new DbColumn('apellidos', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 250,
					'after' => 'nombres'
				)),
				new DbColumn('identificacion', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 20,
					'notNull' => true,
					'after' => 'apellidos'
				)),
				new DbColumn('tipo_documentos_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'identificacion'
				)),
				new DbColumn('ciudad_expedido', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'tipo_documentos_id'
				)),
				new DbColumn('ciudad_nacimiento', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'ciudad_expedido'
				)),
				new DbColumn('fecha_nacimiento', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 10,
					'after' => 'ciudad_nacimiento'
				)),
				new DbColumn('estados_civiles_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'fecha_nacimiento'
				)),
				new DbColumn('sexo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'estados_civiles_id'
				)),
				new DbColumn('direccion_casa', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 80,
					'notNull' => true,
					'after' => 'sexo'
				)),
				new DbColumn('telefono_casa', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 80,
					'notNull' => true,
					'after' => 'direccion_casa'
				)),
				new DbColumn('celular', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 40,
					'after' => 'telefono_casa'
				)),
				new DbColumn('direccion_trabajo', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 80,
					'after' => 'celular'
				)),
				new DbColumn('telefono_trabajo', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 80,
					'after' => 'direccion_trabajo'
				)),
				new DbColumn('fax', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 80,
					'after' => 'telefono_trabajo'
				)),
				new DbColumn('apartado_aereo', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 80,
					'after' => 'fax'
				)),
				new DbColumn('direccion_correspondencia', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 80,
					'after' => 'apartado_aereo'
				)),
				new DbColumn('correo_1', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 80,
					'notNull' => true,
					'after' => 'direccion_correspondencia'
				)),
				new DbColumn('correo_2', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 80,
					'after' => 'correo_1'
				)),
				new DbColumn('correo_3', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 80,
					'after' => 'correo_2'
				)),
				new DbColumn('tipo_socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'correo_3'
				)),
				new DbColumn('tipos_pago_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'tipo_socios_id'
				)),
				new DbColumn('formas_pago_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'tipos_pago_id'
				)),
				new DbColumn('numero_tarjeta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'formas_pago_id'
				)),
				new DbColumn('envia_correo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'numero_tarjeta'
				)),
				new DbColumn('estados_socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'notNull' => true,
					'after' => 'envia_correo'
				)),
				new DbColumn('cobra', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'estados_socios_id'
				)),
				new DbColumn('fecha_retiro', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'cobra'
				)),
				new DbColumn('imagen_socio', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 100,
					'after' => 'fecha_retiro'
				)),
				new DbColumn('ciudad_casa', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'imagen_socio'
				)),
				new DbColumn('ciudad_trabajo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'ciudad_casa'
				)),
				new DbColumn('celular_trabajo', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 40,
					'after' => 'ciudad_trabajo'
				)),
				new DbColumn('nombre_padre', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 80,
					'after' => 'celular_trabajo'
				)),
				new DbColumn('nombre_madre', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 80,
					'after' => 'nombre_padre'
				)),
				new DbColumn('imprime', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'nombre_madre'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'socios_id'
				)),
				new DbIndex('socios_id_index', array(
					'socios_id'
				)),
				new DbIndex('socios_2_index', array(
					'numero_accion',
					'identificacion'
				)),
				new DbIndex('socios_3_index', array(
					'estados_socios_id'
				)),
				new DbIndex('socios_1_index', array(
					'socios_id'
				)),
				new DbIndex('socios_4_index', array(
					'numero_accion',
					'identificacion'
				)),
				new DbIndex('socios_5_index', array(
					'numero_accion',
					'identificacion',
					'estados_socios_id'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '518',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
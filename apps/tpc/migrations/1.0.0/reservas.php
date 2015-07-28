<?php 

class ReservasMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('reservas', array(
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
				new DbColumn('numero_contrato', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 20,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('fecha_compra', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'numero_contrato'
				)),
				new DbColumn('tipo_documentos_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'fecha_compra'
				)),
				new DbColumn('identificacion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 14,
					'notNull' => true,
					'after' => 'tipo_documentos_id'
				)),
				new DbColumn('apellidos', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 60,
					'notNull' => true,
					'after' => 'identificacion'
				)),
				new DbColumn('nombres', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 60,
					'notNull' => true,
					'after' => 'apellidos'
				)),
				new DbColumn('direccion_residencia', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 50,
					'notNull' => true,
					'after' => 'nombres'
				)),
				new DbColumn('ciudad_residencia', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'direccion_residencia'
				)),
				new DbColumn('telefono_residencia', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 20,
					'notNull' => true,
					'after' => 'ciudad_residencia'
				)),
				new DbColumn('correo', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 60,
					'after' => 'telefono_residencia'
				)),
				new DbColumn('celular', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 20,
					'notNull' => true,
					'after' => 'correo'
				)),
				new DbColumn('empresa', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 100,
					'after' => 'celular'
				)),
				new DbColumn('direccion_trabajo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 50,
					'after' => 'empresa'
				)),
				new DbColumn('ciudades_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'direccion_trabajo'
				)),
				new DbColumn('telefono_trabajo', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 20,
					'after' => 'ciudades_id'
				)),
				new DbColumn('profesiones_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'telefono_trabajo'
				)),
				new DbColumn('cargo', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 100,
					'after' => 'profesiones_id'
				)),
				new DbColumn('envio_correspondencia', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'cargo'
				)),
				new DbColumn('tipo_socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'envio_correspondencia'
				)),
				new DbColumn('estado_movimiento', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 2,
					'notNull' => true,
					'after' => 'tipo_socios_id'
				)),
				new DbColumn('estado_contrato', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 2,
					'notNull' => true,
					'after' => 'estado_movimiento'
				)),
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'after' => 'estado_contrato'
				)),
				new DbColumn('estados_civiles_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'after' => 'socios_id'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('tipo_docuemntos_id', array(
					'tipo_documentos_id'
				)),
				new DbIndex('ciudad_residencia', array(
					'ciudad_residencia'
				)),
				new DbIndex('identificacion', array(
					'identificacion'
				)),
				new DbIndex('numero_contrato', array(
					'numero_contrato'
				)),
				new DbIndex('nombres', array(
					'nombres'
				)),
				new DbIndex('apellidos', array(
					'apellidos'
				)),
				new DbIndex('ciudades_id', array(
					'ciudades_id'
				)),
				new DbIndex('profesiones_id', array(
					'profesiones_id'
				)),
				new DbIndex('tipo_socios_id', array(
					'tipo_socios_id'
				)),
				new DbIndex('socios_id', array(
					'socios_id'
				)),
				new DbIndex('estado_contrato', array(
					'estado_contrato'
				)),
				new DbIndex('estado_movimiento', array(
					'estado_movimiento'
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
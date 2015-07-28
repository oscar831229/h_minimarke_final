<?php 

class ConyugesMigration_105 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('conyuges', array(
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
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('apellidos', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 60,
					'after' => 'socios_id'
				)),
				new DbColumn('nombres', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 60,
					'after' => 'apellidos'
				)),
				new DbColumn('fecha_nacimiento', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'nombres'
				)),
				new DbColumn('tipo_documentos_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'fecha_nacimiento'
				)),
				new DbColumn('identificacion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 14,
					'after' => 'tipo_documentos_id'
				)),
				new DbColumn('profesiones_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'identificacion'
				)),
				new DbColumn('estados_civiles_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'profesiones_id'
				)),
				new DbColumn('direccion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'estados_civiles_id'
				)),
				new DbColumn('telefono', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'direccion'
				)),
				new DbColumn('celular', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'after' => 'telefono'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'celular'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('socios_id', array(
					'socios_id'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '9260',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
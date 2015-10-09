<?php 

class RechazosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rechazos', array(
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
				new DbColumn('tipo_documentos_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('identificacion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'notNull' => true,
					'after' => 'tipo_documentos_id'
				)),
				new DbColumn('apellidos', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'identificacion'
				)),
				new DbColumn('nombres', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'apellidos'
				)),
				new DbColumn('fecha_solicitud', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'nombres'
				)),
				new DbColumn('observaciones', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'fecha_solicitud'
				)),
				new DbColumn('primera_fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'observaciones'
				)),
				new DbColumn('primera_acta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'after' => 'primera_fecha'
				)),
				new DbColumn('primera_observacion', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'primera_acta'
				)),
				new DbColumn('primera_estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'primera_observacion'
				)),
				new DbColumn('segunda_fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'primera_estado'
				)),
				new DbColumn('segunda_acta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'after' => 'segunda_fecha'
				)),
				new DbColumn('segunda_observacion', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'segunda_acta'
				)),
				new DbColumn('segunda_estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'segunda_observacion'
				)),
				new DbColumn('tercera_fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'segunda_estado'
				)),
				new DbColumn('tercera_acta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'after' => 'tercera_fecha'
				)),
				new DbColumn('tercera_observacion', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'tercera_acta'
				)),
				new DbColumn('tercera_estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'tercera_observacion'
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
				'AUTO_INCREMENT' => '4',
				'ENGINE' => 'MyISAM',
				'TABLE_COLLATION' => 'latin1_general_ci'
			)
		));
	}

}
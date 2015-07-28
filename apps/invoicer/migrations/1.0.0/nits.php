<?php 

class NitsMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('nits', array(
			'columns' => array(
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('clase', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'nit'
				)),
				new DbColumn('tipodoc', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'clase'
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 120,
					'notNull' => true,
					'after' => 'tipodoc'
				)),
				new DbColumn('direccion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 25,
					'after' => 'nombre'
				)),
				new DbColumn('telefono', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'direccion'
				)),
				new DbColumn('ciudad', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'telefono'
				)),
				new DbColumn('locciu', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'ciudad'
				)),
				new DbColumn('autoret', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'locciu'
				)),
				new DbColumn('fax', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'after' => 'autoret'
				)),
				new DbColumn('contacto', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 35,
					'after' => 'fax'
				)),
				new DbColumn('estado_nit', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'contacto'
				)),
				new DbColumn('resp_iva', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'estado_nit'
				)),
				new DbColumn('cupo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'resp_iva'
				)),
				new DbColumn('tipo_nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'after' => 'cupo'
				)),
				new DbColumn('ap_aereo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 2,
					'after' => 'tipo_nit'
				)),
				new DbColumn('grab', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'ap_aereo'
				)),
				new DbColumn('plazo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'grab'
				)),
				new DbColumn('lista', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'plazo'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'nit'
				)),
				new DbIndex('ix124_3', array(
					'nombre'
				)),
				new DbIndex('clase', array(
					'clase'
				)),
				new DbIndex('locciu', array(
					'locciu'
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
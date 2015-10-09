<?php 

class CargosFijosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('cargos_fijos', array(
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
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'nombre'
				)),
				new DbColumn('cuenta_contable', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 20,
					'notNull' => true,
					'after' => 'valor'
				)),
				new DbColumn('naturaleza', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'cuenta_contable'
				)),
				new DbColumn('cuenta_iva', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 20,
					'notNull' => true,
					'after' => 'naturaleza'
				)),
				new DbColumn('centro_costos', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 5,
					'notNull' => true,
					'after' => 'cuenta_iva'
				)),
				new DbColumn('centro_costos_iva', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 5,
					'notNull' => true,
					'after' => 'centro_costos'
				)),
				new DbColumn('iva', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'centro_costos_iva'
				)),
				new DbColumn('porcentaje_iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 2,
					'notNull' => true,
					'after' => 'iva'
				)),
				new DbColumn('mora', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'porcentaje_iva'
				)),
				new DbColumn('tipo_cargo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'mora'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'tipo_cargo'
				)),
				new DbColumn('cuenta_consolidar', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 20,
					'notNull' => true,
					'after' => 'estado'
				)),
				new DbColumn('ingreso_tercero', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'cuenta_consolidar'
				)),
				new DbColumn('tercero_fijo', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 20,
					'after' => 'ingreso_tercero'
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
				'AUTO_INCREMENT' => '37',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_general_ci'
			)
		));
	}

}
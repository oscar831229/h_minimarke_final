<?php 

class CargosSociosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('cargos_socios', array(
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
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('periodo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'periodo'
				)),
				new DbColumn('cargos_fijos_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'cargos_fijos_id'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'descripcion'
				)),
				new DbColumn('iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'valor'
				)),
				new DbColumn('cuota_aplicar', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'iva'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'cuota_aplicar'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('cargos_socios_1_index', array(
					'periodo',
					'estado'
				)),
				new DbIndex('cargos_socios_2_index', array(
					'socios_id',
					'periodo'
				)),
				new DbIndex('socios_17_index', array(
					'socios_id'
				)),
				new DbIndex('socios_18_index', array(
					'socios_id',
					'periodo'
				)),
				new DbIndex('socios_19_index', array(
					'socios_id',
					'periodo',
					'estado'
				)),
				new DbIndex('socios_20_index', array(
					'periodo',
					'estado'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '24879',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_general_ci'
			)
		));
	}

}
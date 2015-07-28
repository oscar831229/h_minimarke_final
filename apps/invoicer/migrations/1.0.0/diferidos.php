<?php 

class DiferidosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('diferidos', array(
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
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 120,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('grupo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'descripcion'
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'grupo'
				)),
				new DbColumn('fecha_compra', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'centro_costo'
				)),
				new DbColumn('valor_compra', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 16,
					'scale' => 2,
					'notNull' => true,
					'after' => 'fecha_compra'
				)),
				new DbColumn('numero_fac', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'valor_compra'
				)),
				new DbColumn('meses_a_dep', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 4,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'numero_fac'
				)),
				new DbColumn('proveedor', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'notNull' => true,
					'after' => 'meses_a_dep'
				)),
				new DbColumn('forma_pago', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'proveedor'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'forma_pago'
				)),
				new DbColumn('entrada', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'after' => 'estado'
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
				'AUTO_INCREMENT' => '1',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
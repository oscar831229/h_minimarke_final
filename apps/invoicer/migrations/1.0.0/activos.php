<?php 

class ActivosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('activos', array(
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
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 35,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('grupo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'notNull' => true,
					'after' => 'descripcion'
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'notNull' => true,
					'after' => 'grupo'
				)),
				new DbColumn('tipos_activos_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'centro_costo'
				)),
				new DbColumn('cantidad', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'tipos_activos_id'
				)),
				new DbColumn('fecha_compra', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'cantidad'
				)),
				new DbColumn('valor_compra', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'fecha_compra'
				)),
				new DbColumn('numero_fac', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'after' => 'valor_compra'
				)),
				new DbColumn('serie', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'after' => 'numero_fac'
				)),
				new DbColumn('proveedor', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'after' => 'serie'
				)),
				new DbColumn('responsable', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'notNull' => true,
					'after' => 'proveedor'
				)),
				new DbColumn('ubicacion', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'responsable'
				)),
				new DbColumn('meses_a_dep', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 4,
					'notNull' => true,
					'after' => 'ubicacion'
				)),
				new DbColumn('meses_dep', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'after' => 'meses_a_dep'
				)),
				new DbColumn('dep_acumulada', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'meses_dep'
				)),
				new DbColumn('valor_ajus', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'dep_acumulada'
				)),
				new DbColumn('paag_acumulado', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'valor_ajus'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'paag_acumulado'
				)),
				new DbColumn('inventariado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'estado'
				)),
				new DbColumn('fecha_inv', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'inventariado'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('codigo', array(
					'codigo'
				)),
				new DbIndex('centro_costo', array(
					'centro_costo'
				)),
				new DbIndex('tipos_activos_id', array(
					'tipos_activos_id'
				)),
				new DbIndex('responsable', array(
					'responsable'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '5036',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
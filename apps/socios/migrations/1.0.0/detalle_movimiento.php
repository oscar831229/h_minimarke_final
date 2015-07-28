<?php 

class DetalleMovimientoMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('detalle_movimiento', array(
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
				new DbColumn('movimiento_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'movimiento_id'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('fecha_venc', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('tipo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'fecha_venc'
				)),
				new DbColumn('cargos_socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'tipo'
				)),
				new DbColumn('recibos_caja_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'cargos_socios_id'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'recibos_caja_id'
				)),
				new DbColumn('iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'valor'
				)),
				new DbColumn('total', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'iva'
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 120,
					'notNull' => true,
					'after' => 'total'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'descripcion'
				)),
				new DbColumn('tipo_documento', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'after' => 'estado'
				)),
				new DbColumn('tipo_movi', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'tipo_documento'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('detalle_movi_1_index', array(
					'socios_id',
					'estado'
				)),
				new DbIndex('detalle_movi_2_index', array(
					'movimiento_id',
					'estado'
				)),
				new DbIndex('socios_8_index', array(
					'socios_id'
				)),
				new DbIndex('socios_9_index', array(
					'socios_id',
					'estado'
				)),
				new DbIndex('socios_10_index', array(
					'movimiento_id'
				)),
				new DbIndex('socios_11_index', array(
					'movimiento_id',
					'estado'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '110542',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_general_ci'
			)
		));
	}

}
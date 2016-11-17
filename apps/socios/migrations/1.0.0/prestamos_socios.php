<?php 

class PrestamosSociosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('prestamos_socios', array(
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
				new DbColumn('valor_financiacion', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 20,
					'scale' => 3,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('fecha_prestamo', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'valor_financiacion'
				)),
				new DbColumn('fecha_inicio', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'fecha_prestamo'
				)),
				new DbColumn('numero_cuotas', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'fecha_inicio'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'numero_cuotas'
				)),
				new DbColumn('interes_corriente', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 2,
					'notNull' => true,
					'after' => 'estado'
				)),
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 6,
					'after' => 'interes_corriente'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'comprob'
				)),
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'notNull' => true,
					'after' => 'numero'
				)),
				new DbColumn('cuenta_cruce', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'notNull' => true,
					'after' => 'cuenta'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('socios_23_index', array(
					'socios_id',
					'estado'
				)),
				new DbIndex('socios_24_index', array(
					'socios_id'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '3487',
				'ENGINE' => 'MyISAM',
				'TABLE_COLLATION' => 'latin1_general_ci'
			)
		));
	}

}
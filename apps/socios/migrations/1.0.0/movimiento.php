<?php 

class MovimientoMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('movimiento', array(
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
				new DbColumn('factura_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('periodo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'notNull' => true,
					'after' => 'factura_id'
				)),
				new DbColumn('fecha_at', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'periodo'
				)),
				new DbColumn('saldo_anterior', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'fecha_at'
				)),
				new DbColumn('mora', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'saldo_anterior'
				)),
				new DbColumn('cargos_mes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'mora'
				)),
				new DbColumn('saldo_actual', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'cargos_mes'
				)),
				new DbColumn('iva_mora', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'saldo_actual'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('socios_7_index', array(
					'socios_id'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '18930',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_general_ci'
			)
		));
	}

}
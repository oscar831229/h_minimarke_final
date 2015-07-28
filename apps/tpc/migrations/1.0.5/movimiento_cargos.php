<?php 

class MovimientoCargosMigration_105 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('movimiento_cargos', array(
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
				new DbColumn('periodo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('fecha_at', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'periodo'
				)),
				new DbColumn('numero_factura', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'fecha_at'
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
				'TABLE_COLLATION' => 'latin1_general_ci'
			)
		));
	}

}
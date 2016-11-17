<?php 

class EstadosFacturaMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('estados_factura', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 2,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 60,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'nombre'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'codigo'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
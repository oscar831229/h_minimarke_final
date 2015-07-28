<?php 

class PagoSaldoMigration_103 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('pago_saldo', array(
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
				new DbColumn('numero_cuotas', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('interes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 2,
					'after' => 'numero_cuotas'
				)),
				new DbColumn('fecha_primera_cuota', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 10,
					'notNull' => true,
					'after' => 'interes'
				)),
				new DbColumn('premios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'fecha_primera_cuota'
				)),
				new DbColumn('observaciones', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 100,
					'after' => 'premios_id'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('socios_id', array(
					'socios_id'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '977',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
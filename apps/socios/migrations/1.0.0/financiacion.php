<?php 

class FinanciacionMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('financiacion', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('factura_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('ultimo_abono', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 3,
					'notNull' => true,
					'after' => 'factura_id'
				)),
				new DbColumn('fecha_ultimo', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'ultimo_abono'
				)),
				new DbColumn('saldo_anterior', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 3,
					'notNull' => true,
					'after' => 'fecha_ultimo'
				)),
				new DbColumn('anterior_interes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 3,
					'notNull' => true,
					'after' => 'saldo_anterior'
				)),
				new DbColumn('cuota', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 3,
					'notNull' => true,
					'after' => 'anterior_interes'
				)),
				new DbColumn('saldo_actual', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 3,
					'notNull' => true,
					'after' => 'cuota'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'saldo_actual'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'nit'
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
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
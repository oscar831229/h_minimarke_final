<?php 

class PagosAutomaticosMigration_105 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('pagos_automaticos', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('numero_tarjeta', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 20,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('formas_pago_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'numero_tarjeta'
				)),
				new DbColumn('fecha_exp', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'formas_pago_id'
				)),
				new DbColumn('fecha_ven', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'fecha_exp'
				)),
				new DbColumn('bancos_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'fecha_ven'
				)),
				new DbColumn('digito_verificacion', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 4,
					'notNull' => true,
					'after' => 'bancos_id'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'digito_verificacion'
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
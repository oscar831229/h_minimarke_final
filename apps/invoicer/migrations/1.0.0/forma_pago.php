<?php 

class FormaPagoMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('forma_pago', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 70,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('cta_contable', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'notNull' => true,
					'after' => 'descripcion'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'cta_contable'
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
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
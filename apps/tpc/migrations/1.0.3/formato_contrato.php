<?php 

class FormatoContratoMigration_103 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('formato_contrato', array(
			'columns' => array(
				new DbColumn('tipo_contrato_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('formato', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 50,
					'notNull' => true,
					'after' => 'tipo_contrato_id'
				)),
				new DbColumn('usa_formato', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'formato'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'tipo_contrato_id'
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
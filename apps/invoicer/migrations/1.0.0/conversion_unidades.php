<?php 

class ConversionUnidadesMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('conversion_unidades', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('unidad', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('unidad_base', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'after' => 'unidad'
				)),
				new DbColumn('factor_conversion', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 16,
					'scale' => 8,
					'after' => 'unidad_base'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('unidad', array(
					'unidad'
				)),
				new DbIndex('unidad_base', array(
					'unidad_base'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '1',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
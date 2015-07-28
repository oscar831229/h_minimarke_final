<?php 

class WconceptoMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('wconcepto', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nom_concepto', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'after' => 'codigo'
				)),
				new DbColumn('recargo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 4,
					'after' => 'nom_concepto'
				)),
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'recargo'
				))
			),
			'indexes' => array(
				new DbIndex('l_wconcepto', array(
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
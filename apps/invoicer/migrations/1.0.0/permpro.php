<?php 

class PermproMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('permpro', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('programa', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'codigo'
				))
			),
			'indexes' => array(
				new DbIndex('l_permpro', array(
					'codigo',
					'programa'
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
<?php 

class PaagMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('paag', array(
			'columns' => array(
				new DbColumn('ano_mes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'first' => true
				)),
				new DbColumn('paag', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 4,
					'after' => 'ano_mes'
				))
			),
			'indexes' => array(
				new DbIndex('l_paag', array(
					'ano_mes'
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
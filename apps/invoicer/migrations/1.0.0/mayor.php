<?php 

class MayorMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('mayor', array(
			'columns' => array(
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'notNull' => true,
					'after' => 'cuenta'
				))
			),
			'indexes' => array(

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
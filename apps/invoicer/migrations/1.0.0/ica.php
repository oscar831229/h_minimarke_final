<?php 

class IcaMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('ica', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 2,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('otros', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 25,
					'after' => 'cuenta'
				))
			),
			'indexes' => array(
				new DbIndex('l_ica', array(
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
<?php 

class RsegmenMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rsegmen', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nom_rsegmen', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 25,
					'after' => 'codigo'
				)),
				new DbColumn('esventa', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'nom_rsegmen'
				))
			),
			'indexes' => array(
				new DbIndex('l_rsegmen', array(
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
<?php 

class ComcierMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('comcier', array(
			'columns' => array(
				new DbColumn('cuentai', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('cuentaf', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cuentai'
				)),
				new DbColumn('cuenta3', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cuentaf'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'cuenta3'
				))
			),
			'indexes' => array(
				new DbIndex('l_comcier', array(
					'cuentai'
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
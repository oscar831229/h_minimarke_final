<?php 

class RmovimiMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rmovimi', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nreser', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('concep', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'notNull' => true,
					'after' => 'nreser'
				)),
				new DbColumn('valorm', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'notNull' => true,
					'after' => 'concep'
				)),
				new DbColumn('fecham', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'valorm'
				)),
				new DbColumn('numdoc', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'fecham'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'numdoc'
				))
			),
			'indexes' => array(
				new DbIndex('l_rmovimi', array(
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
<?php 

class ChequesMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('cheques', array(
			'columns' => array(
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'after' => 'comprob'
				)),
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'notNull' => true,
					'after' => 'numero'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'cuenta'
				)),
				new DbColumn('num_cheque', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 8,
					'scale' => 0,
					'after' => 'nit'
				))
			),
			'indexes' => array(
				new DbIndex('l_cheques', array(
					'comprob',
					'numero'
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
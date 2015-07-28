<?php 

class RreserhMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rreserh', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('secuen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'notNull' => true,
					'after' => 'secuen'
				)),
				new DbColumn('habita', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'nit'
				)),
				new DbColumn('numadu', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'habita'
				)),
				new DbColumn('numnin', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'numadu'
				)),
				new DbColumn('numadi', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'numnin'
				))
			),
			'indexes' => array(
				new DbIndex('l_rreserh', array(
					'codigo',
					'secuen'
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
<?php 

class RpagosfMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rpagosf', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nreser', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('concep', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'nreser'
				)),
				new DbColumn('valorp', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'notNull' => true,
					'after' => 'concep'
				)),
				new DbColumn('formap', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'notNull' => true,
					'after' => 'valorp'
				)),
				new DbColumn('numdoc', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'formap'
				)),
				new DbColumn('fechap', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'numdoc'
				))
			),
			'indexes' => array(
				new DbIndex('l_rpagosf', array(
					'codigo',
					'nreser'
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
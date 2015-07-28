<?php 

class Qx$schemaMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('qx__$schema', array(
			'columns' => array(
				new DbColumn('TABLE_NAME', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 30,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('COLUMN_NAME', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 30,
					'notNull' => true,
					'after' => 'TABLE_NAME'
				)),
				new DbColumn('INFORMIX_TYPE_CODE', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 6,
					'notNull' => true,
					'after' => 'COLUMN_NAME'
				)),
				new DbColumn('INFORMIX_TYPE_DATA', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'INFORMIX_TYPE_CODE'
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
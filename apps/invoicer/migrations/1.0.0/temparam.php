<?php 

class TemparamMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('temparam', array(
			'columns' => array(
				new DbColumn('nit1_a', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'nit1_a'
				)),
				new DbColumn('definitiva_a', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('retiro_a', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'definitiva_a'
				)),
				new DbColumn('dispo_a', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'retiro_a'
				))
			),
			'indexes' => array(
				new DbIndex('l_temparam', array(
					'nit1_a'
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
<?php 

class Empresa1Migration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('empresa1', array(
			'columns' => array(
				new DbColumn('f_cierref', array(
					'type' => DbColumn::TYPE_DATE,
					'first' => true
				)),
				new DbColumn('f_cierrep', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'f_cierref'
				)),
				new DbColumn('ano_c', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'after' => 'f_cierrep'
				)),
				new DbColumn('base_ret', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'after' => 'ano_c'
				)),
				new DbColumn('otros', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 50,
					'after' => 'base_ret'
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
<?php 

class BancosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('bancos', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'after' => 'codigo'
				)),
				new DbColumn('bancos', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'cuenta'
				)),
				new DbColumn('oficina', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 5,
					'after' => 'bancos'
				)),
				new DbColumn('ciudad', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'after' => 'oficina'
				))
			),
			'indexes' => array(
				new DbIndex('l_bancos', array(
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
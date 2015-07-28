<?php 

class PresMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('pres', array(
			'columns' => array(
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'after' => 'cuenta'
				)),
				new DbColumn('ano', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 4,
					'primary' => true,
					'notNull' => true,
					'after' => 'centro_costo'
				)),
				new DbColumn('mes', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 2,
					'primary' => true,
					'notNull' => true,
					'after' => 'ano'
				)),
				new DbColumn('pres', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'mes'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'cuenta',
					'centro_costo',
					'ano',
					'mes'
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
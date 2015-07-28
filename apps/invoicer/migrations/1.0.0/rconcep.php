<?php 

class RconcepMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rconcep', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nom_rconcep', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'after' => 'codigo'
				)),
				new DbColumn('coltra', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'nom_rconcep'
				)),
				new DbColumn('codiva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'coltra'
				)),
				new DbColumn('poriva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 4,
					'after' => 'codiva'
				)),
				new DbColumn('contab', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'poriva'
				))
			),
			'indexes' => array(
				new DbIndex('l_rconcep', array(
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
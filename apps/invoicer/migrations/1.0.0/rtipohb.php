<?php 

class RtipohbMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rtipohb', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nom_rtipohb', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('numtot', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'notNull' => true,
					'after' => 'nom_rtipohb'
				)),
				new DbColumn('numvac', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'numtot'
				)),
				new DbColumn('numres', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'numvac'
				)),
				new DbColumn('numocu', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'numres'
				)),
				new DbColumn('numblo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'numocu'
				)),
				new DbColumn('numase', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'numblo'
				))
			),
			'indexes' => array(
				new DbIndex('l_rtipohb', array(
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
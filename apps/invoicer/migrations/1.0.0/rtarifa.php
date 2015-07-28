<?php 

class RtarifaMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rtarifa', array(
			'columns' => array(
				new DbColumn('ano', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'notNull' => true,
					'after' => 'ano'
				)),
				new DbColumn('claset', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'codigo'
				)),
				new DbColumn('nom_rtarifa', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'claset'
				)),
				new DbColumn('vlrhab', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'nom_rtarifa'
				)),
				new DbColumn('vlrdes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'vlrhab'
				)),
				new DbColumn('vlralm', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'vlrdes'
				)),
				new DbColumn('vlrcen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'vlralm'
				)),
				new DbColumn('vlrrf1', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'vlrcen'
				)),
				new DbColumn('vlrrf2', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'vlrrf1'
				)),
				new DbColumn('vlrbeb', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'vlrrf2'
				)),
				new DbColumn('vlrali', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'vlrbeb'
				))
			),
			'indexes' => array(
				new DbIndex('l_rtarifa', array(
					'ano',
					'codigo',
					'claset'
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
<?php 

class CriterioMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('criterio', array(
			'columns' => array(
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'first' => true
				)),
				new DbColumn('almacen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'comprob'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'almacen'
				)),
				new DbColumn('sc', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'numero'
				)),
				new DbColumn('pr', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'sc'
				)),
				new DbColumn('maj', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'pr'
				)),
				new DbColumn('tra', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'maj'
				)),
				new DbColumn('up', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'tra'
				)),
				new DbColumn('cte', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'up'
				)),
				new DbColumn('fra', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'cte'
				)),
				new DbColumn('pd', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'fra'
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
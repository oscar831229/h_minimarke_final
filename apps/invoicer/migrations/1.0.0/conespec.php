<?php 

class ConespecMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('conespec', array(
			'columns' => array(
				new DbColumn('programa', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('semestre', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'after' => 'programa'
				)),
				new DbColumn('dias_bon', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'semestre'
				)),
				new DbColumn('valor_bon', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'dias_bon'
				)),
				new DbColumn('proporc', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'valor_bon'
				)),
				new DbColumn('todo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'proporc'
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
<?php 

class RreserdMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rreserd', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('secuen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('valord', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'notNull' => true,
					'after' => 'secuen'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'valord'
				)),
				new DbColumn('formap', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'notNull' => true,
					'after' => 'numero'
				)),
				new DbColumn('fechad', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'formap'
				))
			),
			'indexes' => array(
				new DbIndex('l_rreserd', array(
					'codigo',
					'secuen'
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
<?php 

class FondosaMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('fondosa', array(
			'columns' => array(
				new DbColumn('clase', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'clase'
				)),
				new DbColumn('codemp', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'primary' => true,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'primary' => true,
					'notNull' => true,
					'after' => 'codemp'
				)),
				new DbColumn('codautor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('valori', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'codautor'
				)),
				new DbColumn('valorl', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'valori'
				))
			),
			'indexes' => array(
				new DbIndex('l_fondosa', array(
					'clase',
					'codigo',
					'codemp',
					'fecha'
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
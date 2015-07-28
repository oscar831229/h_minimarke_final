<?php 

class InteresUsuraMigration_104 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('interes_usura', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('fecha_inicial', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('fecha_final', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'fecha_inicial'
				)),
				new DbColumn('interes_trimestral', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 2,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'fecha_final'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'interes_trimestral'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '8',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
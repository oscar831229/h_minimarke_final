<?php 

class PlanillaMigration_104 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('planilla', array(
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
				new DbColumn('fecha_planilla', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'id'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('index_2_planilla', array(
					'fecha_planilla'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '1007',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
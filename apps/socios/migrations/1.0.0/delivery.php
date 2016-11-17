<?php 

class DeliveryMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('delivery', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('numfac', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('periodo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'numfac'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'periodo'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('relay_key', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'estado'
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
				'AUTO_INCREMENT' => '3862',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
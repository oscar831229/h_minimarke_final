<?php 

class RefinanciarMigration_101 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('refinanciar', array(
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
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('numero_cuotas', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'valor'
				)),
				new DbColumn('interes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 2,
					'notNull' => true,
					'after' => 'numero_cuotas'
				)),
				new DbColumn('fecha_primera_cuota', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'interes'
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
				'AUTO_INCREMENT' => '3',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
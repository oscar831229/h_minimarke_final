<?php 

class AjusteSaldosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('ajuste_saldos', array(
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
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 5,
					'after' => 'id'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'comprob'
				)),
				new DbColumn('periodo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 6,
					'notNull' => true,
					'after' => 'numero'
				)),
				new DbColumn('fecha_hora', array(
					'type' => DbColumn::TYPE_DATETIME,
					'notNull' => true,
					'after' => 'periodo'
				)),
				new DbColumn('usuarios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'fecha_hora'
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
				'AUTO_INCREMENT' => '1',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
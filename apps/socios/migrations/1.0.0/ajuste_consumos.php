<?php 

class AjusteConsumosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('ajuste_consumos', array(
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
				new DbColumn('prefijo', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 5,
					'after' => 'id'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'prefijo'
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
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'notNull' => true,
					'after' => 'fecha_hora'
				)),
				new DbColumn('usuarios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'valor'
				)),
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'usuarios_id'
				)),
				new DbColumn('iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'notNull' => true,
					'after' => 'socios_id'
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
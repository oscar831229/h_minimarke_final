<?php 

class TipoContratoMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('tipo_contrato', array(
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
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('sigla', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'notNull' => true,
					'after' => 'nombre'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'notNull' => true,
					'after' => 'sigla'
				)),
				new DbColumn('formato', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 64,
					'after' => 'numero'
				)),
				new DbColumn('usa_formato', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'formato'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'usa_formato'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('numero', array(
					'numero'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '4',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
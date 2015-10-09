<?php 

class ExplaboralMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('explaboral', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('empresa', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 100,
					'after' => 'socios_id'
				)),
				new DbColumn('direccion', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 100,
					'after' => 'empresa'
				)),
				new DbColumn('cargo', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 100,
					'after' => 'direccion'
				)),
				new DbColumn('telefono', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 20,
					'after' => 'cargo'
				)),
				new DbColumn('fax', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 20,
					'after' => 'telefono'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'fax'
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
				'AUTO_INCREMENT' => '76182',
				'ENGINE' => 'MyISAM',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
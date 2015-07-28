<?php 

class CorrespondenciaSociosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('correspondencia_socios', array(
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
				new DbColumn('tipo_correspondencia_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 200,
					'after' => 'tipo_correspondencia_id'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATETIME,
					'notNull' => true,
					'after' => 'descripcion'
				)),
				new DbColumn('name', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 100,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('type', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 100,
					'notNull' => true,
					'after' => 'name'
				)),
				new DbColumn('size', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 45,
					'notNull' => true,
					'after' => 'type'
				)),
				new DbColumn('content', array(
					'type' => DbColumn::TYPE_TEXT,
					'notNull' => true,
					'after' => 'size'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'content'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('fk_correspondencia_socios_1', array(
					'tipo_correspondencia_id'
				))
			),
			'references' => array(
				new DbReference('fk_correspondencia_socios_1', array(
					'referencedSchema' => 'hfos_socios',
					'referencedTable' => 'tipo_correspondencia',
					'columns' => array('tipo_correspondencia_id'),
					'referencedColumns' => array('id')
				))
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
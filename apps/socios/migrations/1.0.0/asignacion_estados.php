<?php 

class AsignacionEstadosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('asignacion_estados', array(
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
				new DbColumn('estados_socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('fecha_ini', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'estados_socios_id'
				)),
				new DbColumn('fecha_fin', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'fecha_ini'
				)),
				new DbColumn('observaciones', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'fecha_fin'
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
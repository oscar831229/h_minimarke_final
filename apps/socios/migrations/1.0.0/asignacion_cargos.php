<?php 

class AsignacionCargosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('asignacion_cargos', array(
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
					'size' => 11,
					'after' => 'id'
				)),
				new DbColumn('cargos_fijos_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'cargos_fijos_id'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('cargos_fijos_id', array(
					'cargos_fijos_id'
				)),
				new DbIndex('socios_6_index', array(
					'socios_id'
				))
			),
			'references' => array(
				new DbReference('asignacion_cargos_ibfk_1', array(
					'referencedSchema' => 'hfos_socios',
					'referencedTable' => 'cargos_fijos',
					'columns' => array('cargos_fijos_id'),
					'referencedColumns' => array('id')
				))
			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '65223',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
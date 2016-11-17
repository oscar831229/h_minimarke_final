<?php 

class AccionEstadosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('accion_estados', array(
			'columns' => array(
				new DbColumn('estados_socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('cargos_fijos_id_ini', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'after' => 'estados_socios_id'
				)),
				new DbColumn('cargos_fijos_id_fin', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'cargos_fijos_id_ini'
				)),
				new DbColumn('borrar_cargo_fijo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'cargos_fijos_id_fin'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'estados_socios_id',
					'cargos_fijos_id_ini'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '',
				'ENGINE' => 'MyISAM',
				'TABLE_COLLATION' => 'utf8_general_ci'
			)
		));
	}

}
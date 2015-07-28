<?php 

class ModulosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('modulos', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('submodulo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'codigo'
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'after' => 'submodulo'
				))
			),
			'indexes' => array(
				new DbIndex('l_modulos', array(
					'codigo',
					'submodulo'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
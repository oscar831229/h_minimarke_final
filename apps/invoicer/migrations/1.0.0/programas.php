<?php 

class ProgramasMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('programas', array(
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
				new DbColumn('programa', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'notNull' => true,
					'after' => 'submodulo'
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'after' => 'programa'
				)),
				new DbColumn('f_ingreso', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'descripcion'
				))
			),
			'indexes' => array(
				new DbIndex('l_progr1', array(
					'codigo',
					'submodulo',
					'programa'
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
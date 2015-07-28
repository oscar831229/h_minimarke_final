<?php 

class EmpresaMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('empresa', array(
			'columns' => array(
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 55,
					'after' => 'nit'
				)),
				new DbColumn('f_cierrec', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'nombre'
				)),
				new DbColumn('f_cierrei', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'f_cierrec'
				)),
				new DbColumn('f_cierren', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'f_cierrei'
				)),
				new DbColumn('seis', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'f_cierren'
				)),
				new DbColumn('sop', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'seis'
				)),
				new DbColumn('cinco', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'sop'
				)),
				new DbColumn('contabiliza', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'cinco'
				)),
				new DbColumn('presupuesto', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'after' => 'contabiliza'
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'after' => 'presupuesto'
				)),
				new DbColumn('version', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 7,
					'after' => 'centro_costo'
				))
			),
			'indexes' => array(
				new DbIndex('l_empresa', array(
					'nit'
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
<?php 

class ConceptoMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('concepto', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nom_concepto', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'after' => 'codigo'
				)),
				new DbColumn('vacaciones', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'nom_concepto'
				)),
				new DbColumn('aportes', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'vacaciones'
				)),
				new DbColumn('prestacion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'aportes'
				)),
				new DbColumn('base_iss', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'prestacion'
				)),
				new DbColumn('retencion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'base_iss'
				)),
				new DbColumn('porc_ret', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 8,
					'scale' => 7,
					'after' => 'retencion'
				)),
				new DbColumn('salario', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'porc_ret'
				)),
				new DbColumn('porc_salario', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 4,
					'after' => 'salario'
				)),
				new DbColumn('recargo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 4,
					'after' => 'porc_salario'
				)),
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'recargo'
				)),
				new DbColumn('contra', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cuenta'
				)),
				new DbColumn('netea', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'contra'
				))
			),
			'indexes' => array(
				new DbIndex('l_concepto', array(
					'codigo'
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
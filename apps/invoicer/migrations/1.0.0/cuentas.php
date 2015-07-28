<?php 

class CuentasMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('cuentas', array(
			'columns' => array(
				new DbColumn('tipo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('mayor', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'tipo'
				)),
				new DbColumn('clase', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 2,
					'after' => 'mayor'
				)),
				new DbColumn('subclase', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 2,
					'after' => 'clase'
				)),
				new DbColumn('auxiliar', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'subclase'
				)),
				new DbColumn('subaux', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'auxiliar'
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'subaux'
				)),
				new DbColumn('es_auxiliar', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'nombre'
				)),
				new DbColumn('pide_nit', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'es_auxiliar'
				)),
				new DbColumn('pide_ban', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'pide_nit'
				)),
				new DbColumn('pide_base', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'pide_ban'
				)),
				new DbColumn('porc_iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 7,
					'scale' => 5,
					'after' => 'pide_base'
				)),
				new DbColumn('pide_fact', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'porc_iva'
				)),
				new DbColumn('pide_centro', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'pide_fact'
				)),
				new DbColumn('es_mayor', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'pide_centro'
				)),
				new DbColumn('contrapartida', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'es_mayor'
				)),
				new DbColumn('cta_retencion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'contrapartida'
				)),
				new DbColumn('porc_retenc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 4,
					'after' => 'cta_retencion'
				)),
				new DbColumn('cta_iva', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'porc_retenc'
				)),
				new DbColumn('porcen_iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 4,
					'after' => 'cta_iva'
				)),
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'primary' => true,
					'notNull' => true,
					'after' => 'porcen_iva'
				))
			),
			'indexes' => array(
				new DbIndex('l_cuentas', array(
					'cuenta'
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
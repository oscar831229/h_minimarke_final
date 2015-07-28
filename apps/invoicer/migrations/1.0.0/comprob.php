<?php 

class ComprobMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('comprob', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nom_comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'codigo'
				)),
				new DbColumn('diario', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'notNull' => true,
					'after' => 'nom_comprob'
				)),
				new DbColumn('cta_iva', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'diario'
				)),
				new DbColumn('cta_ivad', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_iva'
				)),
				new DbColumn('cta_ivam', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_ivad'
				)),
				new DbColumn('cta_cartera', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_ivam'
				)),
				new DbColumn('cta_iva16_venta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_cartera'
				)),
				new DbColumn('cta_iva10_venta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_iva16_venta'
				)),
				new DbColumn('pide_vend', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'cta_iva10_venta'
				)),
				new DbColumn('consecutivo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'pide_vend'
				)),
				new DbColumn('comprob_contab', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'consecutivo'
				))
			),
			'indexes' => array(
				new DbIndex('l_comprob', array(
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
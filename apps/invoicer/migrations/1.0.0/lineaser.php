<?php 

class LineaserMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('lineaser', array(
			'columns' => array(
				new DbColumn('linea', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 35,
					'notNull' => true,
					'after' => 'linea'
				)),
				new DbColumn('cta_gasto', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'notNull' => true,
					'after' => 'descripcion'
				)),
				new DbColumn('cta_iva', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_gasto'
				)),
				new DbColumn('porc_iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 7,
					'scale' => 5,
					'after' => 'cta_iva'
				)),
				new DbColumn('cta_retiva', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'porc_iva'
				)),
				new DbColumn('cta_retencion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_retiva'
				)),
				new DbColumn('cta_cartera', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_retencion'
				)),
				new DbColumn('cta_ex1', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_cartera'
				)),
				new DbColumn('cta_ex2', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_ex1'
				))
			),
			'indexes' => array(

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
<?php 

class Comprob1Migration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('comprob1', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('cta_ivap', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'codigo'
				)),
				new DbColumn('cta_ivadp', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_ivap'
				)),
				new DbColumn('cta_iva7', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_ivadp'
				)),
				new DbColumn('cta_iva7p', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_iva7'
				)),
				new DbColumn('cta_retiva7', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_iva7p'
				)),
				new DbColumn('cta_iva3', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_retiva7'
				)),
				new DbColumn('cta_iva3p', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_iva3'
				)),
				new DbColumn('cta_retiva3', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_iva3p'
				)),
				new DbColumn('otros', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 36,
					'after' => 'cta_retiva3'
				))
			),
			'indexes' => array(
				new DbIndex('l_comprob1', array(
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
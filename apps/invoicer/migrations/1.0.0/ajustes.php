<?php 

class AjustesMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('ajustes', array(
			'columns' => array(
				new DbColumn('tipo_reg', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'primary' => true,
					'notNull' => true,
					'after' => 'tipo_reg'
				)),
				new DbColumn('cta_debito', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cuenta'
				)),
				new DbColumn('cta_credito', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cta_debito'
				))
			),
			'indexes' => array(
				new DbIndex('l_ajustes', array(
					'tipo_reg',
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
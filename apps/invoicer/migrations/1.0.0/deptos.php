<?php 

class DeptosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('deptos', array(
			'columns' => array(
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'centro_costo'
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'after' => 'codigo'
				)),
				new DbColumn('otros', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'after' => 'descripcion'
				))
			),
			'indexes' => array(
				new DbIndex('l_deptos', array(
					'centro_costo',
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
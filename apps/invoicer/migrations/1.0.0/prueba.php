<?php 

class PruebaMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('prueba', array(
			'columns' => array(
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'notNull' => true,
					'after' => 'cuenta'
				)),
				new DbColumn('tipo_doc', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'after' => 'nit'
				)),
				new DbColumn('numero_doc', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'tipo_doc'
				)),
				new DbColumn('ano_mes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'after' => 'numero_doc'
				)),
				new DbColumn('debe', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'ano_mes'
				)),
				new DbColumn('haber', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'debe'
				)),
				new DbColumn('saldo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'haber'
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
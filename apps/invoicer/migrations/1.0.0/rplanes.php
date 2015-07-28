<?php 

class RplanesMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rplanes', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nom_rplanes', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 25,
					'after' => 'codigo'
				)),
				new DbColumn('poscta', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'after' => 'nom_rplanes'
				)),
				new DbColumn('formafac', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'poscta'
				)),
				new DbColumn('incluyed', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'formafac'
				)),
				new DbColumn('incluyea', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'incluyed'
				)),
				new DbColumn('incluyec', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'incluyea'
				)),
				new DbColumn('codigofc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'incluyec'
				))
			),
			'indexes' => array(
				new DbIndex('l_rplanes', array(
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
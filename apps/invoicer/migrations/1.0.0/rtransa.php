<?php 

class RtransaMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rtransa', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nom_rtransa', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'after' => 'codigo'
				)),
				new DbColumn('ctadeb', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'nom_rtransa'
				)),
				new DbColumn('ctodeb', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'after' => 'ctadeb'
				)),
				new DbColumn('nitdeb', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'ctodeb'
				)),
				new DbColumn('ctacre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'nitdeb'
				)),
				new DbColumn('ctocre', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'after' => 'ctacre'
				)),
				new DbColumn('nitcre', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'ctocre'
				)),
				new DbColumn('descrp', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 25,
					'after' => 'nitcre'
				)),
				new DbColumn('comprb', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'descrp'
				))
			),
			'indexes' => array(
				new DbIndex('l_rtransa', array(
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
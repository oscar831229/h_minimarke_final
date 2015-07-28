<?php 

class RtarjetMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rtarjet', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nom_rtarjet', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 30,
					'after' => 'codigo'
				)),
				new DbColumn('nitb', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'notNull' => true,
					'after' => 'nom_rtarjet'
				)),
				new DbColumn('porce_com', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 4,
					'after' => 'nitb'
				)),
				new DbColumn('porcb_ret', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 4,
					'after' => 'porce_com'
				)),
				new DbColumn('porce_ret', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 4,
					'after' => 'porcb_ret'
				)),
				new DbColumn('interfcon', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'porce_ret'
				))
			),
			'indexes' => array(
				new DbIndex('l_rtarjet', array(
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
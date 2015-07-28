<?php 

class RetencionMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('retencion', array(
			'columns' => array(
				new DbColumn('limite_sup', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 5,
					'scale' => 0,
					'first' => true
				)),
				new DbColumn('valor_ret', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 8,
					'scale' => 0,
					'after' => 'limite_sup'
				)),
				new DbColumn('porc_ret', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 4,
					'after' => 'valor_ret'
				))
			),
			'indexes' => array(
				new DbIndex('l_retencion', array(
					'limite_sup'
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
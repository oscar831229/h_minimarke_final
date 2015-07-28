<?php 

class ParamliqMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('paramliq', array(
			'columns' => array(
				new DbColumn('seg', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('ret', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'notNull' => true,
					'after' => 'seg'
				)),
				new DbColumn('aux', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'notNull' => true,
					'after' => 'ret'
				)),
				new DbColumn('vac_x_ano', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'notNull' => true,
					'after' => 'aux'
				)),
				new DbColumn('limite_extras', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 4,
					'after' => 'vac_x_ano'
				)),
				new DbColumn('prima_sem', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'notNull' => true,
					'after' => 'limite_extras'
				)),
				new DbColumn('dias_n', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'notNull' => true,
					'after' => 'prima_sem'
				)),
				new DbColumn('comprob_nom', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'dias_n'
				)),
				new DbColumn('comprob_pro', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'comprob_nom'
				)),
				new DbColumn('egmfp', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 5,
					'notNull' => true,
					'after' => 'comprob_pro'
				)),
				new DbColumn('egmfe', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 5,
					'notNull' => true,
					'after' => 'egmfp'
				)),
				new DbColumn('ivmp', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 5,
					'notNull' => true,
					'after' => 'egmfe'
				)),
				new DbColumn('ivme', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 5,
					'notNull' => true,
					'after' => 'ivmp'
				)),
				new DbColumn('atep', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 8,
					'scale' => 7,
					'after' => 'ivme'
				)),
				new DbColumn('minimo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'notNull' => true,
					'after' => 'atep'
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
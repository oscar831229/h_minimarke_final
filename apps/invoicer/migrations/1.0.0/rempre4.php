<?php 

class Rempre4Migration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('rempre4', array(
			'columns' => array(
				new DbColumn('nitt', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nitd', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'notNull' => true,
					'after' => 'nitt'
				)),
				new DbColumn('nitc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'nitd'
				)),
				new DbColumn('nitp', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'nitc'
				)),
				new DbColumn('nitg', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'nitp'
				)),
				new DbColumn('f_cierrer', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'nitg'
				)),
				new DbColumn('seghot', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 8,
					'scale' => 2,
					'after' => 'f_cierrer'
				)),
				new DbColumn('porc_turi', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 4,
					'after' => 'seghot'
				)),
				new DbColumn('codig_hab', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'porc_turi'
				)),
				new DbColumn('codig_ali', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'codig_hab'
				)),
				new DbColumn('codig_beb', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'codig_ali'
				)),
				new DbColumn('codig_seg', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'codig_beb'
				)),
				new DbColumn('codig_abo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'codig_seg'
				)),
				new DbColumn('porcs_tel', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 4,
					'after' => 'codig_abo'
				)),
				new DbColumn('porcb_iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 4,
					'after' => 'porcs_tel'
				)),
				new DbColumn('contabiliza', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'porcb_iva'
				)),
				new DbColumn('ctaefe', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'contabiliza'
				)),
				new DbColumn('ctache', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'ctaefe'
				)),
				new DbColumn('ctatar', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'ctache'
				)),
				new DbColumn('ctacon', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'ctatar'
				)),
				new DbColumn('ctahue', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'ctacon'
				)),
				new DbColumn('ctatel', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'ctahue'
				)),
				new DbColumn('ctaseg', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'ctatel'
				)),
				new DbColumn('ctapro', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'ctaseg'
				)),
				new DbColumn('comven', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'ctapro'
				)),
				new DbColumn('coming', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'comven'
				)),
				new DbColumn('posubt', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'coming'
				))
			),
			'indexes' => array(
				new DbIndex('l_rempre4', array(
					'nitt'
				)),
				new DbIndex('ix265_10', array(
					'codig_hab'
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
<?php 

class PeriodoMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('periodo', array(
			'columns' => array(
				new DbColumn('periodo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('cierre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'periodo'
				)),
				new DbColumn('facturado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'cierre'
				)),
				new DbColumn('intereses_mora', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 3,
					'notNull' => true,
					'after' => 'facturado'
				)),
				new DbColumn('dia_factura', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 2,
					'after' => 'intereses_mora'
				)),
				new DbColumn('dias_plazo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 2,
					'after' => 'dia_factura'
				)),
				new DbColumn('consecutivos_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'dias_plazo'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'periodo'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_general_ci'
			)
		));
	}

}
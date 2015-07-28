<?php 

class WcuotasMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('wcuotas', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('clase', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('secuencia', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'clase'
				)),
				new DbColumn('num_cuota', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'after' => 'secuencia'
				)),
				new DbColumn('f_vence', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'num_cuota'
				)),
				new DbColumn('capital', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 11,
					'scale' => 0,
					'notNull' => true,
					'after' => 'f_vence'
				)),
				new DbColumn('intereses', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 11,
					'scale' => 0,
					'notNull' => true,
					'after' => 'capital'
				)),
				new DbColumn('int_mora', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 11,
					'scale' => 0,
					'after' => 'intereses'
				)),
				new DbColumn('dias_mora', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'after' => 'int_mora'
				)),
				new DbColumn('f_pago', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'dias_mora'
				)),
				new DbColumn('abono_parcial', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 11,
					'scale' => 0,
					'after' => 'f_pago'
				)),
				new DbColumn('saldo_cuota', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 11,
					'scale' => 0,
					'after' => 'abono_parcial'
				)),
				new DbColumn('saldo_prestamo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 11,
					'scale' => 0,
					'notNull' => true,
					'after' => 'saldo_cuota'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'saldo_prestamo'
				))
			),
			'indexes' => array(
				new DbIndex('l_wcuotas', array(
					'codigo',
					'clase',
					'secuencia',
					'num_cuota'
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
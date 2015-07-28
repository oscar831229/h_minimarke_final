<?php 

class MaestroMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('maestro', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('cedula', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('primer_apellido', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'notNull' => true,
					'after' => 'cedula'
				)),
				new DbColumn('segund_apellido', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'after' => 'primer_apellido'
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'notNull' => true,
					'after' => 'segund_apellido'
				)),
				new DbColumn('direccion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 35,
					'after' => 'nombre'
				)),
				new DbColumn('telefono', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'after' => 'direccion'
				)),
				new DbColumn('cargo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'notNull' => true,
					'after' => 'telefono'
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'notNull' => true,
					'after' => 'cargo'
				)),
				new DbColumn('sexo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'centro_costo'
				)),
				new DbColumn('e_civil', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'sexo'
				)),
				new DbColumn('libreta_mil', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 9,
					'after' => 'e_civil'
				)),
				new DbColumn('retfte', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'after' => 'libreta_mil'
				)),
				new DbColumn('porc_ret', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 4,
					'after' => 'retfte'
				)),
				new DbColumn('contrato', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'notNull' => true,
					'after' => 'porc_ret'
				)),
				new DbColumn('forma_pago', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'notNull' => true,
					'after' => 'contrato'
				)),
				new DbColumn('fondo_ces', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'after' => 'forma_pago'
				)),
				new DbColumn('ubica', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'after' => 'fondo_ces'
				)),
				new DbColumn('eps', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'notNull' => true,
					'after' => 'ubica'
				)),
				new DbColumn('fondo_pens', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 2,
					'scale' => 0,
					'notNull' => true,
					'after' => 'eps'
				)),
				new DbColumn('sueldo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'fondo_pens'
				)),
				new DbColumn('auxilio', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'sueldo'
				)),
				new DbColumn('f_nace', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'auxilio'
				)),
				new DbColumn('f_ingreso', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'f_nace'
				)),
				new DbColumn('f_retiro', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'f_ingreso'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 1,
					'scale' => 0,
					'notNull' => true,
					'after' => 'f_retiro'
				)),
				new DbColumn('vivienda', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'estado'
				)),
				new DbColumn('f_u_pago', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'vivienda'
				)),
				new DbColumn('f_aumento', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'f_u_pago'
				)),
				new DbColumn('f_vence', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'f_aumento'
				)),
				new DbColumn('dias_vacm', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'f_vence'
				)),
				new DbColumn('tiempo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'dias_vacm'
				)),
				new DbColumn('horasd', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'tiempo'
				))
			),
			'indexes' => array(
				new DbIndex('l_maestro', array(
					'codigo'
				)),
				new DbIndex('l_maestro1', array(
					'nombre',
					'primer_apellido'
				)),
				new DbIndex('l_maestro2', array(
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
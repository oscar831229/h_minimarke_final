<?php 

class DetalleCuotahMigration_105 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('detalle_cuotah', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('hoy', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'socios_id'
				)),
				new DbColumn('fecha1', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'hoy'
				)),
				new DbColumn('estado1', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'fecha1'
				)),
				new DbColumn('hoy_pagado', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'estado1'
				)),
				new DbColumn('cuota2', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'hoy_pagado'
				)),
				new DbColumn('fecha2', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'cuota2'
				)),
				new DbColumn('estado2', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'fecha2'
				)),
				new DbColumn('cuota2_pagado', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'estado2'
				)),
				new DbColumn('cuota3', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'cuota2_pagado'
				)),
				new DbColumn('fecha3', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'cuota3'
				)),
				new DbColumn('estado3', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'fecha3'
				)),
				new DbColumn('cuota3_pagado', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'estado3'
				)),
				new DbColumn('nota_historia_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'notNull' => true,
					'after' => 'cuota3_pagado'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('socios_id', array(
					'socios_id'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '10',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
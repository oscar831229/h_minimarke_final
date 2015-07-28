<?php 

class SalidaMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('salida', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 8,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 35,
					'notNull' => true,
					'after' => 'codigo'
				)),
				new DbColumn('responsable', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 0,
					'notNull' => true,
					'after' => 'descripcion'
				)),
				new DbColumn('serie', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'after' => 'responsable'
				)),
				new DbColumn('ubicacion', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'serie'
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'after' => 'ubicacion'
				)),
				new DbColumn('blanco1', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'centro_costo'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'blanco1'
				)),
				new DbColumn('fecha_inv', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'after' => 'estado'
				)),
				new DbColumn('blanco2', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'fecha_inv'
				)),
				new DbColumn('blanco3', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'blanco2'
				)),
				new DbColumn('blanco4', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'blanco3'
				)),
				new DbColumn('blanco5', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'blanco4'
				)),
				new DbColumn('blanco6', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'blanco5'
				)),
				new DbColumn('blanco7', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'blanco6'
				)),
				new DbColumn('blanco8', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'blanco7'
				)),
				new DbColumn('blanco9', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'blanco8'
				))
			),
			'indexes' => array(
				new DbIndex('l_salida', array(
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
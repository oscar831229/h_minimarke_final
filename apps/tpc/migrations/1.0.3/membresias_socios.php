<?php 

class MembresiasSociosMigration_103 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('membresias_socios', array(
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
				new DbColumn('membresias_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('temporadas_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'membresias_id'
				)),
				new DbColumn('capacidad', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'temporadas_id'
				)),
				new DbColumn('puntos_ano', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'capacidad'
				)),
				new DbColumn('numero_anos', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'puntos_ano'
				)),
				new DbColumn('total_puntos', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'numero_anos'
				)),
				new DbColumn('valor_total', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'total_puntos'
				)),
				new DbColumn('cuota_inicial', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'valor_total'
				)),
				new DbColumn('saldo_pagar', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'cuota_inicial'
				)),
				new DbColumn('derecho_afiliacion_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'notNull' => true,
					'after' => 'saldo_pagar'
				)),
				new DbColumn('afiliacion_pagado', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'derecho_afiliacion_id'
				)),
				new DbColumn('estado_cuoafi', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'afiliacion_pagado'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('socios_id', array(
					'socios_id'
				)),
				new DbIndex('membresias_id', array(
					'membresias_id'
				)),
				new DbIndex('temporadas_id', array(
					'temporadas_id'
				)),
				new DbIndex('estado_cuoafi', array(
					'estado_cuoafi'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '991',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
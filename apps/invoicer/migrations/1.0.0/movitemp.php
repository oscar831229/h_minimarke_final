<?php 

class MovitempMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('movitemp', array(
			'columns' => array(
				new DbColumn('sid', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 32,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'primary' => true,
					'notNull' => true,
					'after' => 'sid'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'after' => 'comprob'
				)),
				new DbColumn('consecutivo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'after' => 'numero'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'consecutivo'
				)),
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 18,
					'after' => 'cuenta'
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 6,
					'scale' => 0,
					'after' => 'nit'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'notNull' => true,
					'after' => 'centro_costo'
				)),
				new DbColumn('deb_cre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'valor'
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 35,
					'after' => 'deb_cre'
				)),
				new DbColumn('tipo_doc', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'descripcion'
				)),
				new DbColumn('numero_doc', array(
					'type' => DbColumn::TYPE_INTEGER,
					'after' => 'tipo_doc'
				)),
				new DbColumn('base_grab', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 2,
					'after' => 'numero_doc'
				)),
				new DbColumn('conciliado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'base_grab'
				)),
				new DbColumn('f_vence', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'conciliado'
				)),
				new DbColumn('numfol', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'after' => 'f_vence'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'numfol'
				)),
				new DbColumn('checksum', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 32,
					'notNull' => true,
					'after' => 'estado'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'sid',
					'comprob',
					'numero',
					'consecutivo'
				)),
				new DbIndex('sid', array(
					'sid',
					'comprob',
					'numero'
				)),
				new DbIndex('sid_2', array(
					'sid',
					'comprob',
					'numero',
					'consecutivo'
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
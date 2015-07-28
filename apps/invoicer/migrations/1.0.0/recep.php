<?php 

class RecepMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('recep', array(
			'columns' => array(
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'comprob'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'numero'
				)),
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 18,
					'scale' => 0,
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
				))
			),
			'indexes' => array(
				new DbIndex('l_recep', array(
					'comprob',
					'numero'
				)),
				new DbIndex('l_recep1', array(
					'cuenta',
					'nit',
					'deb_cre'
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
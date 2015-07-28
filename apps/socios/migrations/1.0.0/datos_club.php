<?php 

class DatosClubMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('datos_club', array(
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
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'nit'
				)),
				new DbColumn('nomcad', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'nombre'
				)),
				new DbColumn('nomger', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'nomcad'
				)),
				new DbColumn('ciudad_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'notNull' => true,
					'after' => 'nomger'
				)),
				new DbColumn('direccion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'ciudad_id'
				)),
				new DbColumn('telefono', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'direccion'
				)),
				new DbColumn('fax', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'telefono'
				)),
				new DbColumn('sitweb', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'fax'
				)),
				new DbColumn('email', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 45,
					'after' => 'sitweb'
				)),
				new DbColumn('resfac', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 15,
					'notNull' => true,
					'after' => 'email'
				)),
				new DbColumn('fecfac', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'resfac'
				)),
				new DbColumn('prefac', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 5,
					'after' => 'fecfac'
				)),
				new DbColumn('numfac', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'prefac'
				)),
				new DbColumn('numfai', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'numfac'
				)),
				new DbColumn('numfaf', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'numfai'
				)),
				new DbColumn('numrec', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'numfaf'
				)),
				new DbColumn('imagen', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 200,
					'after' => 'numrec'
				)),
				new DbColumn('numsoc', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'notNull' => true,
					'after' => 'imagen'
				)),
				new DbColumn('version', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 7,
					'after' => 'numsoc'
				)),
				new DbColumn('f_cierre', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'version'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '2',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
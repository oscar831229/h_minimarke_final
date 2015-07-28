<?php 

class CuentasBancosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('cuentas_bancos', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 14,
					'unsigned' => true,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 80,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 30,
					'notNull' => true,
					'after' => 'descripcion'
				)),
				new DbColumn('banco_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 14,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'numero'
				)),
				new DbColumn('tipo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'banco_id'
				)),
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'notNull' => true,
					'after' => 'tipo'
				)),
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'cuenta'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'centro_costo'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('banco_id', array(
					'banco_id'
				)),
				new DbIndex('estado', array(
					'estado'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '1',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
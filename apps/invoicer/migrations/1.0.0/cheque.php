<?php 

class ChequeMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('cheque', array(
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
				new DbColumn('chequeras_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'notNull' => true,
					'after' => 'chequeras_id'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'comprob'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'notNull' => true,
					'after' => 'numero'
				)),
				new DbColumn('numero_cheque', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'nit'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'numero_cheque'
				)),
				new DbColumn('hora', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('fecha_cheque', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'hora'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 16,
					'scale' => 2,
					'notNull' => true,
					'after' => 'fecha_cheque'
				)),
				new DbColumn('beneficiario', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 120,
					'notNull' => true,
					'after' => 'valor'
				)),
				new DbColumn('observaciones', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 120,
					'notNull' => true,
					'after' => 'beneficiario'
				)),
				new DbColumn('impreso', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'observaciones'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'impreso'
				))
			),
			'indexes' => array(
				new DbIndex('PRIMARY', array(
					'id'
				)),
				new DbIndex('chequeras_id', array(
					'chequeras_id'
				)),
				new DbIndex('comprob', array(
					'comprob',
					'numero'
				)),
				new DbIndex('nit', array(
					'nit'
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
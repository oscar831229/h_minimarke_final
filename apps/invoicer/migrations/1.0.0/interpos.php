<?php 

class InterposMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('interpos', array(
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
				new DbColumn('prefac', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 5,
					'after' => 'id'
				)),
				new DbColumn('numfac', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'prefac'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'numfac'
				)),
				new DbColumn('almacen', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'fecha'
				)),
				new DbColumn('tipopro', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'almacen'
				)),
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 10,
					'after' => 'tipopro'
				)),
				new DbColumn('menus_items_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'codigo'
				)),
				new DbColumn('cantidad', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'menus_items_id'
				)),
				new DbColumn('cantidadu', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'cantidad'
				)),
				new DbColumn('valorv', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 16,
					'scale' => 2,
					'after' => 'cantidadu'
				)),
				new DbColumn('n_habita', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'valorv'
				)),
				new DbColumn('n_comanda', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'n_habita'
				)),
				new DbColumn('c_cajero', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'n_comanda'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 14,
					'after' => 'c_cajero'
				)),
				new DbColumn('forma_pago', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 2,
					'after' => 'nit'
				)),
				new DbColumn('descargo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'forma_pago'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'descargo'
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
				'AUTO_INCREMENT' => '673893',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
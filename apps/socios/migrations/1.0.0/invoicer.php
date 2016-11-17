<?php 

class InvoicerMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('invoicer', array(
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
				new DbColumn('consecutivos_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('prefijo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 7,
					'notNull' => true,
					'after' => 'consecutivos_id'
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'prefijo'
				)),
				new DbColumn('resolucion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'notNull' => true,
					'after' => 'numero'
				)),
				new DbColumn('fecha_resolucion', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'resolucion'
				)),
				new DbColumn('numero_inicial', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'fecha_resolucion'
				)),
				new DbColumn('numero_final', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'numero_inicial'
				)),
				new DbColumn('nit', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 18,
					'notNull' => true,
					'after' => 'numero_final'
				)),
				new DbColumn('nombre', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 70,
					'notNull' => true,
					'after' => 'nit'
				)),
				new DbColumn('direccion', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 100,
					'after' => 'nombre'
				)),
				new DbColumn('nit_entregar', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 18,
					'notNull' => true,
					'after' => 'direccion'
				)),
				new DbColumn('nombre_entregar', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 70,
					'notNull' => true,
					'after' => 'nit_entregar'
				)),
				new DbColumn('direccion_entregar', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 100,
					'after' => 'nombre_entregar'
				)),
				new DbColumn('fecha_emision', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'direccion_entregar'
				)),
				new DbColumn('fecha_vencimiento', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'fecha_emision'
				)),
				new DbColumn('nota_factura', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'fecha_vencimiento'
				)),
				new DbColumn('nota_ica', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'nota_factura'
				)),
				new DbColumn('venta16', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'nota_ica'
				)),
				new DbColumn('venta10', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'venta16'
				)),
				new DbColumn('venta0', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'venta10'
				)),
				new DbColumn('iva10', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'venta0'
				)),
				new DbColumn('iva16', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'iva10'
				)),
				new DbColumn('iva0', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'iva16'
				)),
				new DbColumn('pagos', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'iva0'
				)),
				new DbColumn('total', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'notNull' => true,
					'after' => 'pagos'
				)),
				new DbColumn('comprob_inve', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'total'
				)),
				new DbColumn('numero_inve', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'after' => 'comprob_inve'
				)),
				new DbColumn('comprob_contab', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'after' => 'numero_inve'
				)),
				new DbColumn('numero_contab', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'after' => 'comprob_contab'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'numero_contab'
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
				'AUTO_INCREMENT' => '2461',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
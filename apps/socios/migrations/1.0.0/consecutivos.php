<?php 

class ConsecutivosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('consecutivos', array(
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
				new DbColumn('detalle', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 64,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('prefijo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 7,
					'notNull' => true,
					'after' => 'detalle'
				)),
				new DbColumn('resolucion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 20,
					'notNull' => true,
					'after' => 'prefijo'
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
				new DbColumn('numero_actual', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'numero_final'
				)),
				new DbColumn('nota_factura', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'numero_actual'
				)),
				new DbColumn('nota_ica', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'nota_factura'
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
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
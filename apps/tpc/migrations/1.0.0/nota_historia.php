<?php 

class NotaHistoriaMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('nota_historia', array(
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
				new DbColumn('socios_id_errado', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'id'
				)),
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'socios_id_errado'
				)),
				new DbColumn('fecha_nota', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'after' => 'fecha_nota'
				)),
				new DbColumn('observaciones', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'valor'
				)),
				new DbColumn('rc_errados', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 250,
					'after' => 'observaciones'
				)),
				new DbColumn('rc_abonar', array(
					'type' => DbColumn::TYPE_VARCHAR,
					'size' => 250,
					'after' => 'rc_errados'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'rc_abonar'
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
				'AUTO_INCREMENT' => '1',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
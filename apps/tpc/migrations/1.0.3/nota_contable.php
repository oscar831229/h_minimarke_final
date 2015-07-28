<?php 

class NotaContableMigration_103 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('nota_contable', array(
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
				new DbColumn('fecha_nota', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 14,
					'scale' => 4,
					'notNull' => true,
					'after' => 'fecha_nota'
				)),
				new DbColumn('observaciones', array(
					'type' => DbColumn::TYPE_TEXT,
					'after' => 'valor'
				)),
				new DbColumn('rc', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'after' => 'observaciones'
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
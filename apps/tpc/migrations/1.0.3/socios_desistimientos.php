<?php 

class SociosDesistimientosMigration_103 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('socios_desistimientos', array(
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
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('motivo_desistimiento_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 10,
					'notNull' => true,
					'after' => 'fecha'
				)),
				new DbColumn('estado_desistimiento_id', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'motivo_desistimiento_id'
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
				'AUTO_INCREMENT' => '8',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
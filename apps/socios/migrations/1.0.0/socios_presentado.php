<?php 

class SociosPresentadoMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('socios_presentado', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('socios_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('presentado1_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'socios_id'
				)),
				new DbColumn('presentado2_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'presentado1_id'
				)),
				new DbColumn('presentado3_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'notNull' => true,
					'after' => 'presentado2_id'
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
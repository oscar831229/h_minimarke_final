<?php 

class PermcomMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('permcom', array(
			'columns' => array(
				new DbColumn('usuario', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'primary' => true,
					'notNull' => true,
					'after' => 'usuario'
				)),
				new DbColumn('popcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'primary' => true,
					'notNull' => true,
					'after' => 'comprob'
				)),
				new DbColumn('autoriza', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'popcion'
				)),
				new DbColumn('hora_i', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'autoriza'
				)),
				new DbColumn('hora_f', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 8,
					'after' => 'hora_i'
				))
			),
			'indexes' => array(
				new DbIndex('l_permcom', array(
					'usuario',
					'comprob',
					'popcion'
				))
			),
			'references' => array(

			),
			'options' => array(
				'TABLE_TYPE' => 'BASE TABLE',
				'AUTO_INCREMENT' => '',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'latin1_swedish_ci'
			)
		));
	}

}
<?php 

class ConcilMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('concil', array(
			'columns' => array(
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'first' => true
				)),
				new DbColumn('numero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 11,
					'after' => 'comprob'
				)),
				new DbColumn('fecha', array(
					'type' => DbColumn::TYPE_DATE,
					'after' => 'numero'
				)),
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'fecha'
				)),
				new DbColumn('descripcion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 35,
					'after' => 'cuenta'
				)),
				new DbColumn('valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 12,
					'scale' => 2,
					'after' => 'descripcion'
				)),
				new DbColumn('deb_cre', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'valor'
				)),
				new DbColumn('conciliado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'deb_cre'
				))
			),
			'indexes' => array(

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
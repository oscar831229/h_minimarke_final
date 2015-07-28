<?php 

class DocumentosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('documentos', array(
			'columns' => array(
				new DbColumn('codigo', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 3,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('nom_documen', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'after' => 'codigo'
				))
			),
			'indexes' => array(
				new DbIndex('l_documentos', array(
					'codigo'
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
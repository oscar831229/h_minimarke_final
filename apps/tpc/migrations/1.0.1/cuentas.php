<?php 

class CuentasMigration_101 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('cuentas', array(
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
				new DbColumn('banco', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 80,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('cuenta', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 40,
					'notNull' => true,
					'after' => 'banco'
				)),
				new DbColumn('cuenta_contable', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'cuenta'
				)),
				new DbColumn('estado', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'cuenta_contable'
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
				'AUTO_INCREMENT' => '6',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_unicode_ci'
			)
		));
	}

}
<?php 

class FormatoChequeMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('formato_cheque', array(
			'columns' => array(
				new DbColumn('id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 14,
					'primary' => true,
					'notNull' => true,
					'autoIncrement' => true,
					'first' => true
				)),
				new DbColumn('chequeras_id', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 14,
					'unsigned' => true,
					'notNull' => true,
					'after' => 'id'
				)),
				new DbColumn('r_ano', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'chequeras_id'
				)),
				new DbColumn('p_ano', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'r_ano'
				)),
				new DbColumn('r_mes', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'p_ano'
				)),
				new DbColumn('p_mes', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'r_mes'
				)),
				new DbColumn('r_dia', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'p_mes'
				)),
				new DbColumn('p_dia', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'r_dia'
				)),
				new DbColumn('r_valor', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'p_dia'
				)),
				new DbColumn('p_valor', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'r_valor'
				)),
				new DbColumn('r_tercero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'p_valor'
				)),
				new DbColumn('p_tercero', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'r_tercero'
				)),
				new DbColumn('r_suma', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'p_tercero'
				)),
				new DbColumn('p_suma', array(
					'type' => DbColumn::TYPE_INTEGER,
					'size' => 3,
					'notNull' => true,
					'after' => 'r_suma'
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
				'AUTO_INCREMENT' => '3',
				'ENGINE' => 'InnoDB',
				'TABLE_COLLATION' => 'utf8_general_ci'
			)
		));
	}

}
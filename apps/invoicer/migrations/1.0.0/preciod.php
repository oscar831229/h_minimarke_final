<?php 

class PreciodMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('preciod', array(
			'columns' => array(
				new DbColumn('centro_costo', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 4,
					'scale' => 0,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('item', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 15,
					'primary' => true,
					'notNull' => true,
					'after' => 'centro_costo'
				)),
				new DbColumn('preciot', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 8,
					'scale' => 2,
					'after' => 'item'
				)),
				new DbColumn('precio_venta_m', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 10,
					'scale' => 2,
					'after' => 'preciot'
				))
			),
			'indexes' => array(
				new DbIndex('l_preciod', array(
					'centro_costo',
					'item'
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
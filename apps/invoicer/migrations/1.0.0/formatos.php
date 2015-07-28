<?php 

class FormatosMigration_100 extends ActiveRecordMigration {

	public function up(){
		$this->morphTable('formatos', array(
			'columns' => array(
				new DbColumn('comprob', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 6,
					'primary' => true,
					'notNull' => true,
					'first' => true
				)),
				new DbColumn('lineas', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'comprob'
				)),
				new DbColumn('linea_i', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'lineas'
				)),
				new DbColumn('col_cuenta', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'linea_i'
				)),
				new DbColumn('col_descripc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'col_cuenta'
				)),
				new DbColumn('col_centro', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'col_descripc'
				)),
				new DbColumn('col_valor', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'col_centro'
				)),
				new DbColumn('col_documen', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'col_valor'
				)),
				new DbColumn('col_nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'col_documen'
				)),
				new DbColumn('col_debitos', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'col_nit'
				)),
				new DbColumn('col_creditos', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'col_debitos'
				)),
				new DbColumn('carac_nota', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'col_creditos'
				)),
				new DbColumn('n_notas', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'carac_nota'
				)),
				new DbColumn('formas', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'notNull' => true,
					'after' => 'n_notas'
				)),
				new DbColumn('n_cuentas', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'formas'
				)),
				new DbColumn('cta_asociada', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 12,
					'after' => 'n_cuentas'
				)),
				new DbColumn('col_iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'cta_asociada'
				)),
				new DbColumn('col_descritem', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'col_iva'
				)),
				new DbColumn('col_cantidad', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'col_descritem'
				)),
				new DbColumn('col_valorunit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'col_cantidad'
				)),
				new DbColumn('col_desctoitem', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'col_valorunit'
				)),
				new DbColumn('col_medida', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'col_desctoitem'
				)),
				new DbColumn('imputacion', array(
					'type' => DbColumn::TYPE_CHAR,
					'size' => 1,
					'after' => 'col_medida'
				)),
				new DbColumn('y_mesn', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'imputacion'
				)),
				new DbColumn('x_mesn', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_mesn'
				)),
				new DbColumn('y_mes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_mesn'
				)),
				new DbColumn('x_mes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_mes'
				)),
				new DbColumn('y_dia', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_mes'
				)),
				new DbColumn('x_dia', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_dia'
				)),
				new DbColumn('y_ano', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_dia'
				)),
				new DbColumn('x_ano', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_ano'
				)),
				new DbColumn('y_beneficiario', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_ano'
				)),
				new DbColumn('x_beneficiario', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_beneficiario'
				)),
				new DbColumn('y_nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_beneficiario'
				)),
				new DbColumn('x_nit', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_nit'
				)),
				new DbColumn('y_total', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_nit'
				)),
				new DbColumn('x_total', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_total'
				)),
				new DbColumn('y_monto1', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_total'
				)),
				new DbColumn('x_monto1', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_monto1'
				)),
				new DbColumn('y_monto2', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_monto1'
				)),
				new DbColumn('x_monto2', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_monto2'
				)),
				new DbColumn('y_comprob', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_monto2'
				)),
				new DbColumn('x_comprob', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_comprob'
				)),
				new DbColumn('y_cta_banco', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_comprob'
				)),
				new DbColumn('x_cta_banco', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_cta_banco'
				)),
				new DbColumn('y_ciudad', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_cta_banco'
				)),
				new DbColumn('x_ciudad', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_ciudad'
				)),
				new DbColumn('y_girador', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_ciudad'
				)),
				new DbColumn('x_girador', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_girador'
				)),
				new DbColumn('y_nota1', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_girador'
				)),
				new DbColumn('x_nota1', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_nota1'
				)),
				new DbColumn('y_nota2', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_nota1'
				)),
				new DbColumn('x_nota2', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_nota2'
				)),
				new DbColumn('y_n_cheque', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_nota2'
				)),
				new DbColumn('x_n_cheque', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_n_cheque'
				)),
				new DbColumn('y_subtotal', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_n_cheque'
				)),
				new DbColumn('x_subtotal', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_subtotal'
				)),
				new DbColumn('y_iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_subtotal'
				)),
				new DbColumn('x_iva', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_iva'
				)),
				new DbColumn('y_propietario', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_iva'
				)),
				new DbColumn('x_propietario', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_propietario'
				)),
				new DbColumn('y_dirclie', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_propietario'
				)),
				new DbColumn('x_dirclie', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_dirclie'
				)),
				new DbColumn('y_fletes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_dirclie'
				)),
				new DbColumn('x_fletes', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_fletes'
				)),
				new DbColumn('y_orden', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_fletes'
				)),
				new DbColumn('x_orden', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_orden'
				)),
				new DbColumn('y_codvend', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_orden'
				)),
				new DbColumn('x_codvend', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_codvend'
				)),
				new DbColumn('y_mesvence', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_codvend'
				)),
				new DbColumn('x_mesvence', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_mesvence'
				)),
				new DbColumn('y_diavence', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_mesvence'
				)),
				new DbColumn('x_diavence', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_diavence'
				)),
				new DbColumn('y_anovence', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_diavence'
				)),
				new DbColumn('x_anovence', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_anovence'
				)),
				new DbColumn('y_pedido', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_anovence'
				)),
				new DbColumn('x_pedido', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_pedido'
				)),
				new DbColumn('y_mesdesc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_pedido'
				)),
				new DbColumn('x_mesdesc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_mesdesc'
				)),
				new DbColumn('y_diadesc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_mesdesc'
				)),
				new DbColumn('x_diadesc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_diadesc'
				)),
				new DbColumn('y_anodesc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_diadesc'
				)),
				new DbColumn('x_anodesc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_anodesc'
				)),
				new DbColumn('y_valordesc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_anodesc'
				)),
				new DbColumn('x_valordesc', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_valordesc'
				)),
				new DbColumn('y_pagina', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_valordesc'
				)),
				new DbColumn('x_pagina', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_pagina'
				)),
				new DbColumn('y_destino', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_pagina'
				)),
				new DbColumn('x_destino', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_destino'
				)),
				new DbColumn('y_centro', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_destino'
				)),
				new DbColumn('x_centro', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_centro'
				)),
				new DbColumn('y_retencion', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_centro'
				)),
				new DbColumn('x_retencion', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_retencion'
				)),
				new DbColumn('y_factura_c', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_retencion'
				)),
				new DbColumn('x_factura_c', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_factura_c'
				)),
				new DbColumn('y_empresa', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'x_factura_c'
				)),
				new DbColumn('x_empresa', array(
					'type' => DbColumn::TYPE_DECIMAL,
					'size' => 3,
					'scale' => 0,
					'after' => 'y_empresa'
				))
			),
			'indexes' => array(
				new DbIndex('l_formatos', array(
					'comprob'
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
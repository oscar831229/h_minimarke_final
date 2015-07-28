<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * Derecho_AfiliacionController
 *
 * Controlador de Derechos de Afiliacion
 *
 */
class Derecho_AfiliacionController extends HyperFormController {

	static protected $_config = array(
		'model' => 'DerechoAfiliacion',
		'plural' => 'Derechos de Afiliación',
		'single' => 'Derecho de Afiliación',
		'genre' => 'M',
		'tabName' => 'Derecho de Afiliación',
		'preferedOrder' => 'id DESC',
		'icon' => 'formatos.png',
		'ignoreButtons' => array(
			'import'
		),
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'auto' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'tipo_contrato_id' => array(
                'single'    => 'Tipo de Contrato',
                'type'      => 'relation',
                'relation'  => 'TipoContrato',
                'fieldRelation' => 'id',
                'conditionsOnCreate' => 'estado="A"',
                'detail'    => 'nombre',
                'maxlength' => 10,
                'filters'   => array('int')
            ),
			'valor' => array(
				'single' => 'Valor Afiliación',
				'type' => 'int',
				'size' => 6,
				'maxlength' => 10,
				'notSearch' => true,
				'notNull' => true,
				'filters' => array('double')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notNull' => true,
				'values' => array(
					'A' => 'Activo',
					'I' => 'Inactivo'
				),
				'filters' => array('onechar')
			)			
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}
}
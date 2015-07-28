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
 * CentrosController
 *
 * Controlador de los centros de costo
 *
 */
class AlmacenesController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Almacenes',
		'plural' => 'almacenes',
		'single' => 'almacén',
		'genre' => 'M',
		'preferedOrder' => 'nom_almacen',
		'icon' => 'almacen.png',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 3,
				'maxlength' => 3,
				'primary' => true,
				'filters' => array('int')
			),
			'nom_almacen' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 40,
				'filters' => array('striptags', 'extraspaces')
			),
			'clase_almacen' => array(
				'single' => 'Clase',
				'type' => 'closed-domain',
				'values' => array(
					'N' => 'PRINCIPAL',
					'D' => 'DEPENDENCIA'
				),
				'filters' => array('alpha')
			),
			'usuarios_id' => array(
				'single' => 'Almacenista',
				'type' => 'relation',
				'relation' => 'Usuarios',
				'fieldRelation' => 'id',
				'detail' => 'nombre_completo',
				'filters' => array('alpha')
			),
			'centro_costo' => array(
				'single' => 'Centro de Costo',
				'type' => 'relation',
				'relation' => 'Centros',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_centro',
				'filters' => array('int')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'values' => array(
					'A' => 'ACTIVO',
					'I' => 'INACTIVO'
				),
				'filters' => array('alpha')
			),
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}

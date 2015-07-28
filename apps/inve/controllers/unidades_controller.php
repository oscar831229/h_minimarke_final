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
class UnidadesController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Unidad',
		'plural' => 'unidades de medida',
		'single' => 'unidad de medida',
		'genre' => 'F',
		'preferedOrder' => 'nom_unidad',
		'icon' => 'unidades.png',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 3,
				'maxlength' => 3,
				'primary' => true,
				'filters' => array('alpha')
			),
			'nom_unidad' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 40,
				'filters' => array('striptags', 'extraspaces')
			),
			'magnitud' => array(
				'single' => 'Magnitud',
				'type' => 'relation',
				'relation' => 'Magnitudes',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'filters' => array('int')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
